<template>
	<div class="field">
		<div id="wnl-code-editor"></div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	#wnl-code-editor
		min-height: 100%
		min-width: 100%
</style>

<script>
	import * as brace from 'brace'
	import 'brace/ext/modelist'
	import 'brace/ext/themelist'

	let editor
	const modelist  = brace.acequire('ace/ext/modelist')
	const themelist = brace.acequire('ace/ext/themelist')

	export default {
		name: 'wnl-form-code',
		props: ['type', 'name', 'form', 'value'],
		data() {
			return {
				code: '',
				mode: 'html',
				theme: 'chrome',
			}
		},
		methods: {
			setMode () {
				let modeObj = modelist.modesByName[this.mode]

				if (modeObj) {
//					require('brace/mode/' + modeObj.name)
//					editor.getSession().setMode(modeObj.mode)
				}
			},
			setTheme () {
				let themeObj = themelist.themesByName[this.theme]

				if (themeObj) {
					require('brace/theme/' + themeObj.name)
					editor.setTheme(themeObj.theme)
				}
			},
			emitCode () {
				this.$emit('input', editor.getValue())
			}
		},
		mounted () {
			editor = brace.edit('wnl-code-editor')
			this.setMode()
			this.setTheme()
			editor.$blockScrolling = Infinity
			editor.getSession().on('change', this.emitCode)
		},
		watch: {
			value (newValue) {
				if (newValue && newValue !== editor.getValue()) {
					editor.setValue(newValue, -1)
				}
			}
		}
	}
</script>
