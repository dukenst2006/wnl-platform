import axios from 'axios'
import store from 'store' // LocalStorage
import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

// Statuses
// TODO: Mar 9, 2017 - Use config when it's ready
const STATUS_IN_PROGRESS = 'in-progress'
const STATUS_COMPLETE = 'complete'

// Helper functions
function getCourseStoreKey(courseId) {
	return `progress-courses-${courseId}`
}

function getLessonStoreKey(courseId, lessonId) {
	return `progress-courses-${courseId}-lessons-${lessonId}`
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

// API functions
function getUserProgressForCourse(courseId) {
	// return axios.get(getApiUrl('courses/${courseId}/user-progress/${userId}'));
	return new Promise((resolve, reject) => {
		let data = {
				lessons: {
					1: {
						status: STATUS_IN_PROGRESS,
						route: {
							name: 'screens',
							params: {
								courseId: 1,
								lessonId: 1,
								screenId: 4,
								slide: 13,
							},
							query: {},
							meta: {},
						}
					},
				}
			}
		resolve(data)
	})
}

// Initial state
const state = {
	courses: {},
}

// Getters
const getters = {
	progressCourse: (state) => (courseId) => {
		if (state.courses.hasOwnProperty(courseId)) {
			return state.courses[courseId]
		}
	},
	progressGetSavedLesson: (state) => (courseId, lessonId) => {
		// TODO: Mar 13, 2017 - Check Vuex before asking localStorage
		return store.get(getLessonStoreKey(courseId, lessonId))
	},
	progressWasLessonStarted: (state) => (courseId, lessonId) => {
		return state.courses.hasOwnProperty(courseId) &&
			state.courses[courseId].hasOwnProperty(lessonId)
	},
	progressIsLessonInProgress: (state) => (courseId, lessonId) => {
		return courses.hasOwnProperty(courseId) &&
			state.courses[courseId].hasOwnProperty(lessonId) &&
			state.courses[courseId][lessonId].status === STATUS_IN_PROGRESS
	},
	progressIsLessonComplete: (state) => (courseId, lessonId) => {
		return courses.hasOwnProperty(courseId) &&
			state.courses[courseId].hasOwnProperty(lessonId) &&
			state.courses[courseId][lessonId].status === STATUS_COMPLETE
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
	}
}

// Actions
const actions = {
	progressSetupCourse({commit}, courseId) {
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
					.catch(error => console.log(error))
			} else {
				commit(types.PROGRESS_SETUP_COURSE, {
					courseId: courseId,
					progressData: storedProgress
				})
				resolve()
			}
		})
	},
	progressStartLesson({commit, getters}, payload) {
		if (!getters.progressWasLessonStarted(payload.courseId, payload.lessonId)) {
			commit(types.PROGRESS_START_LESSON, payload)
		}
	},
	progressUpdateLesson({commit}, payload) {
		commit(types.PROGRESS_UPDATE_LESSON, payload)
	},
	progressCompleteLesson({commit}, payload) {
		if (getters.progressIsLessonInProgress(payload.courseId, payload.lessonId)) {
			commit(types.PROGRESS_COMPLETE_LESSON, payload)
		}
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}