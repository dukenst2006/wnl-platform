import axios from 'axios'
import store from 'store' // LocalStorage
import _ from 'lodash'
import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

// Statuses
// TODO: Mar 9, 2017 - Use config when it's ready
const STATUS_IN_PROGRESS = 'in-progress'
const STATUS_COMPLETE = 'complete'
const CACHE_VERSION = 1

// Helper functions
function getCourseStoreKey(courseId) {
	return `progress-courses-${courseId}-${CACHE_VERSION}`
}

function getLessonStoreKey(courseId, lessonId) {
	return `progress-courses-${courseId}-lessons-${lessonId}-${CACHE_VERSION}`
}

function saveCourseProgress(payload, status) {
	let storeKey = getCourseStoreKey(payload.courseId),
		courseProgress = store.get(storeKey)

	courseProgress.lessons[payload.lessonId] = {
		status,
		route: payload.route,
	}

	store.set(storeKey, courseProgress)
}

function saveLessonProgress(payload) {
	store.set(getLessonStoreKey(payload.courseId, payload.lessonId), payload.route)
}

function resetLessonProgress(payload) {
	store.remove(getLessonStoreKey(payload.courseId, payload.lessonId))
}

// API functions
function getUserProgressForCourse(courseId) {
	// return axios.get(getApiUrl('courses/${courseId}/user-progress/${userId}'));
	return new Promise((resolve, reject) => {
		let data = {
				lessons: {}
			}
		resolve(data)
	})
}

// Namespace
const namespaced = true

// Initial state
const state = {
	courses: {},
}

// Getters
const getters = {
	getCourse: (state) => (courseId) => {
		if (state.courses.hasOwnProperty(courseId)) {
			return state.courses[courseId]
		}
	},
	wasCourseStarted: (state, getters) => (courseId) => {
		return !_.isEmpty(getters.getCourse(courseId).lessons)
	},
	getSavedLesson: (state) => (courseId, lessonId) => {
		// TODO: Mar 13, 2017 - Check Vuex before asking localStorage
		return store.get(getLessonStoreKey(courseId, lessonId))
	},
	wasLessonStarted: (state) => (courseId, lessonId) => {
		return state.courses.hasOwnProperty(courseId) &&
			state.courses[courseId].lessons.hasOwnProperty(lessonId) &&
			state.courses[courseId].lessons[lessonId].hasOwnProperty('status')
	},
	isLessonInProgress: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_IN_PROGRESS
	},
	getFirstLessonIdInProgress: (state) => (courseId) => {
		let lessons = state.courses[courseId].lessons
		for (var lessonId in lessons) {
			if (lessons[lessonId].status === STATUS_IN_PROGRESS) {
				return lessonId
			}
		}
		return 0
	},
	isLessonComplete: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE
	},
	getCompleteLessons: (state, getters, rootState, rootGetters) => (courseId) => {
		let lesson, lessons = []
		for (var lessonId in state.courses[courseId].lessons) {
			lesson = rootGetters['course/getLesson'](lessonId)
			if (state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE) {
				lessons.push(lesson)
			}
		}
		return lessons
	}
}

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_COURSE] (state, payload) {
		set(state.courses, payload.courseId, payload.progressData)
	},
	[types.PROGRESS_START_LESSON] (state, payload) {
		saveCourseProgress(payload, STATUS_IN_PROGRESS)
		set(state.courses[payload.courseId].lessons, payload.lessonId, {
			status: STATUS_IN_PROGRESS,
			route: payload.route,
		})
	},
	[types.PROGRESS_UPDATE_LESSON] (state, payload) {
		saveLessonProgress(payload)
		set(state.courses[payload.courseId].lessons[payload.lessonId], 'route', payload.route)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, payload) {
		saveCourseProgress(payload, STATUS_COMPLETE)
		set(state.courses[payload.courseId].lessons[payload.lessonId], 'status', STATUS_COMPLETE)
		resetLessonProgress(payload)
	}
}

// Actions
const actions = {
	setupCourse({commit}, courseId) {
		return new Promise((resolve, reject) => {
			let storeKey = getCourseStoreKey(courseId),
				storedProgress = store.get(storeKey)

			if (typeof storedProgress !== 'object') {
				getUserProgressForCourse(courseId)
					.then(data => {
						store.set(storeKey, data)
						commit(types.PROGRESS_SETUP_COURSE, {
							courseId: courseId,
							progressData: data
						})
						resolve()
					})
					.catch(exception => $wnl.logger.capture(exception))
			} else {
				commit(types.PROGRESS_SETUP_COURSE, {
					courseId: courseId,
					progressData: storedProgress
				})
				resolve()
			}
		})
	},
	startLesson({commit, getters}, payload) {
		if (!getters.wasLessonStarted(payload.courseId, payload.lessonId)) {
			$wnl.logger.info(`Starting lesson ${payload.lessonId}`, payload)
			commit(types.PROGRESS_START_LESSON, payload)
		}
	},
	updateLesson({commit}, payload) {
		$wnl.logger.debug(`Updating lesson ${payload.lessonId}`)
		commit(types.PROGRESS_UPDATE_LESSON, payload)
	},
	completeLesson({commit, getters}, payload) {
		if (!getters.isLessonComplete(payload.courseId, payload.lessonId)) {
			$wnl.logger.info(`Completing lesson ${payload.lessonId}`, payload)
			commit(types.PROGRESS_COMPLETE_LESSON, payload)
		}
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
