<template>
	<div class="quill-container">
		<div ref="quill">
			<slot></slot>
		</div>
	</div>
</template>

<script>
	import _ from 'lodash'
	import Quill from 'quill'
	import { set } from 'vue'

	import { formInput } from 'js/mixins/form-input'
	import { fontColors } from 'js/utils/colors'

	const defaults = {
		theme: 'snow',
		modules: {},
		placeholder: 'Pisz tutaj...',
	}

	export default {
		name: 'Quill',
		mixins: [formInput],
		props: {
			options: {
				type: Object,
				default: () => ({}),
			},
			toolbar: {
				type: Array,
				default() {
					return [
						[{ 'header': [false, 1, 2, 3] }],
						['bold', 'italic', 'underline', 'link'],
						[{ color: fontColors }],
						['clean'],
						[{ list: 'ordered' }, { list: 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
					]
				}
			},
			autofocus: {
				type: Boolean,
				default: true,
			},
			name: String,
			theme: String,
		},
		data () {
			return {
				focused: this.autofocus,
				quill: null,
				editor: null,
			}
		},
		computed: {
			default() {
				return ''
			},
			quillOptions() {
				let options = _.merge(defaults, this.options)
				options.modules.toolbar = this.toolbar

				return options
			},
		},
		methods: {
			onTextChange() {
				this.setValue(this.editor.innerHTML)
			}
		},
		mounted () {
			this.quill = new Quill(this.$refs.quill, this.quillOptions)
			this.editor = this.$refs.quill.firstElementChild
			this.quill.on('text-change', this.onTextChange)
		},
		watch: {
			focused (val) {
				this.editor[val ? 'focus' : 'blur']()
			},
			inputValue (newValue) {
				if (newValue !== this.editor.innerHTML) {
					this.editor.innerHTML = newValue
				}
			}
		}
	}
</script>
