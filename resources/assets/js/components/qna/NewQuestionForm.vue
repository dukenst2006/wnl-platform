<template>
	<wnl-form
		class="qna-new-question-form"
		hideDefaultSubmit="true"
		name="QnaNewQuestion"
		method="post"
		suppressEnter="true"
		resetAfterSubmit="true"
		resourceRoute="qna_questions"
		:attach="attachedData"
		@submitSuccess="onSubmitSuccess">
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ placeholder: 'O co chcesz zapytać?' }"
			:toolbar="toolbar">
		</wnl-quill>

		<div class="level">
			<div class="level-left"></div>
			<div class="level-right">
				<div class="level-item">
					<wnl-submit cssClass="button is-small is-primary">
						Zapisz
					</wnl-submit>
				</div>
			</div>
		</div>
	</wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.qna-new-question-form
		.ql-container
			height: auto

		.ql-editor
			font-family: $font-family-sans-serif
			font-weight: $font-weight-bold
			font-size: $font-size-plus-1

			strong
				font-weight: $font-weight-black

			&::before
				font-weight: $font-weight-regular
				font-size: $font-size-minus-1
				line-height: $line-height-plus
</style>

<script>
	import { mapActions } from 'vuex'

	import { Form, Quill, Submit } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'

	export default {
		name: 'NewQuestionForm',
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill,
			'wnl-submit': Submit,
		},
		computed: {
			attachedData() {
				return {
					lesson_id: this.$route.params.lessonId
				}
			},
			toolbar() {
				return [
					['bold', 'italic', 'underline', 'link'],
					[{ color: fontColors }],
					['clean'],
				]
			}
		},
		methods: {
			...mapActions('qna', ['fetchQuestions']),
			onSubmitSuccess() {
				this.$emit('submitSuccess')
				this.fetchQuestions()
			},
		},
	}
</script>
