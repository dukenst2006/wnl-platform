<template>
	<div class="wnl-qna">
		<div class="content">
			Pytania i odpowiedzi <a>#pulmonologia</a> <a>#interna</a>
		</div>
		<div class="wnl-chat-form">
			<wnl-qna-form :lessonId="lessonId"></wnl-qna-form>
		</div>

		<div v-if="questions">
			<wnl-question v-for="question in questions"
						  :question="question"
						  :lessonId="lessonId"
						  :key="question.id">
			</wnl-question>
		</div>
		<div v-else>
			Nie ma jeszcze pytań do tego tematu. Zadaj pytanie...
		</div>

	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-qna
		display: flex
		flex: 1
		flex-direction: column
		justify-content: flex-end
		padding-right: 20px
		padding-top: 20px

</style>
<script>
	import QuestionForm from './QuestionForm.vue'
	import Question from './Question.vue'
	import {mapActions, mapGetters} from 'vuex'

	export default {
		name: 'Qna',
		props: ['lessonId'],
		computed: {
			...mapGetters(['qnaGetQuestions']),
			questions(){
				return this.qnaGetQuestions(this.lessonId)
			},
		},
		methods: {
			...mapActions(['qnaSetQuestions'])
		},
		created() {
			this.qnaSetQuestions(this.lessonId)
		},
		components: {
			'wnl-qna-form': QuestionForm,
			'wnl-question': Question
		}
	}
</script>
