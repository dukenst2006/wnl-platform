<template>
	<div class="nextLesson box">
		<div class="level">
			<div class="level-left">
				<div class="level-item caption">
					{{ heading }}
				</div>
			</div>
		</div>
		<div class="lesson" v-if="hasNextLesson">
			<p class="group">{{ groupName }}</p>
			<p class="name">{{ lessonName }}</p>
			<router-link :to="to" class="button" :class="buttonClass">{{ callToAction }}</router-link>
		</div>
		<div v-else>
			<!-- TODO: Mar 22, 2017 - Obviously, we have to fix it to dynamically calculate availability -->
			<p class="zero-state">{{ callToAction }}</p>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.lesson
		text-align: center

	.group
		text-transform: uppercase

	.name
		font-size: $font-size-plus-6
		line-height: $line-height-plus
		margin-bottom: 0.5em
</style>

<script>
	import { mapGetters } from 'vuex'
	import { resource } from 'js/utils/config'

	const STATUS_NONE = 'none'
	const STATUS_IN_PROGRESS = 'in-progress'
	const STATUS_AVAILABLE = 'available'

	const statusParams = {
		[STATUS_NONE]: {
			heading: 'Gratulacje!',
			callToAction: 'Jesteś na bieżąco, kolejny moduł będzie dostępny jutro. :)',
			buttonClass: '',
		},
		[STATUS_IN_PROGRESS]: {
			heading: 'Lekcja w trakcie',
			callToAction: 'Wróć do lekcji',
			buttonClass: 'is-info',
		},
		[STATUS_AVAILABLE]: {
			heading: 'Następna lekcja',
			callToAction: 'Rozpocznij lekcję',
			buttonClass: 'is-primary',
		},
	}

	export default {
		name: 'NextLesson',
		props: ['courseId'],
		computed: {
			...mapGetters([
				'getGroup',
				'getLessons',
				'getLesson',
				'isLessonAvailable',
				'progressWasLessonStarted',
				'progressGetFirstLessonIdInProgress',
			]),
			nextLesson() {
				let lesson = { status: STATUS_NONE },
					inProgressId = this.progressGetFirstLessonIdInProgress(this.courseId)

				if (inProgressId > 0) {
					lesson = this.getLesson(inProgressId)
					lesson.status = STATUS_IN_PROGRESS
				} else {
					for (const lessonId in this.getLessons) {
						if (this.isLessonAvailable(lessonId) &&
							!this.progressWasLessonStarted(this.courseId, lessonId)
						) {
							lesson = this.getLesson(lessonId)
							lesson.status = STATUS_AVAILABLE
							break
						}
					}
				}

				return lesson
			},
			hasNextLesson() {
				return this.nextLesson.status !== STATUS_NONE
			},
			heading() {
				return this.getParam('heading')
			},
			callToAction() {
				return this.getParam('callToAction')
			},
			buttonClass() {
				return this.getParam('buttonClass')
			},
			groupName() {
				return this.getGroup(this.nextLesson.groups).name
			},
			lessonName() {
				return this.nextLesson.name
			},
			to() {
				return {
					name: resource('lessons'),
					params: {
						courseId: this.courseId,
						lessonId: this.nextLesson.id,
					}
				}
			},
		},
		methods: {
			getParam(name) {
				return statusParams[this.nextLesson.status][name]
			}
		}
	}
</script>