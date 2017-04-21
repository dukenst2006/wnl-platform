<template>
	<article class="media wnl-chat-message">
		<figure class="media-left">
			<wnl-avatar :username="user.full_name"></wnl-avatar>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta">
					<strong>{{ user.full_name }}</strong>
					<small>{{ question.time }}</small>
				</p>
				<p class="wnl-message-content">
					{{ question.text }}
				</p>
				<wnl-answer-form :questionId="question.id"
								 :lessonId="lessonId">
				</wnl-answer-form>
			</div>

			<wnl-answer v-if="answers"
						v-for="answer in answers"
						:answer="answer"
						:questionId="question.id"
						:lessonId="lessonId"
						:key="answer.id">
			</wnl-answer>
		</div>
	</article>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'


</style>
<script>
	import {mapActions, mapGetters} from 'vuex'
	import Answer from './Answer.vue'
	import AnswerForm from './AnswerForm.vue'

	export default {
		name: 'Question',
		props: ['question', 'lessonId'],
		computed: {
			...mapGetters(['qnaGetUser', 'qnaGetAnswers']),
			user() {
				return this.qnaGetUser(this.lessonId, this.question.users[0])
			},
			answers() {
				if (this.question.hasOwnProperty('answers')) {
					return this.qnaGetAnswers(this.lessonId, this.question.answers)
				} else {
					return false
				}
			},
			hasAnswers() {
			}
		},
		methods: {},
		mounted() {
		},
		components: {
			'wnl-answer': Answer,
			'wnl-answer-form': AnswerForm
		}
	}
</script>
