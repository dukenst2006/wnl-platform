<template>
	<article class="media">
		<figure class="media-left">
			<wnl-avatar :username="currentUserFullName"></wnl-avatar>
		</figure>
		<div class="media-content">
			<p class="control">
				<textarea :id="inputId" v-model="message" class="textarea wnl-form-textarea"
					@keydown="calculateTextareaHeight"
					@keydown.enter="suppressEnter"
					@keyup.enter="sendMessage"
					:disabled="disabled"
					>
				</textarea>
			</p>
			<div class="message is-warning" v-if="error.length > 0">
				<div class="message-body">{{ error }}</div>
			</div>
		</div>
		<div class="media-right">
			<wnl-image-button
				name="wnl-chat-form-submit"
				icon="send-message"
				alt="Wyślij wiadomość"
				:disabled="sendingDisabled"
				@buttonclicked="sendMessage">
			</wnl-image-button>
		</div>
	</article>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-form-textarea
		min-height: map-get($rounded-square-sizes, 'medium')
		overflow: hidden
		padding: 6px 10px
		resize: none
</style>

<script>
	import { mapGetters } from 'vuex'

	export default{
		props: ['loaded', 'socket', 'room', 'inputId'],
		data(){
			return {
				disabled: false,
				error: '',
				message: '',
				canvasContext: null,
				textarea: {},
				computedStyles: {}
			}
		},
		computed: {
			...mapGetters([
				'currentUserFullName'
			]),
			sendingDisabled() {
				return !this.loaded || this.message.length === 0
			},
			textareaHeight() {
				if (this.canvasContext !== null &&
					!this.textareaHeightChecked &&
					this.message.length > 0
				) {
					this.textareaHeightChecked = true
					return this.calculateTextareaHeight(this.message)
				} else {
					return null
				}
			}
		},
		methods: {
			setTextareaStyles() {
				this.computedStyles = window.getComputedStyle(this.textarea)
			},
			setTextMeasureCanvas() {
				if (this.canvasContext === null) {
					let canvas, context

					canvas = this.measureCanvas || document.createElement('canvas')
					context = canvas.getContext('2d')
					context.font = `${this.computedStyles.fontSize} ${this.computedStyles.fontFamily}`
					this.canvasContext = context
				}

				return true
			},
			calculateTextareaHeight(event) {
				// Don't bother checking the height if Enter was pressed
				if (event.keyCode === 13) {
					return null
				}

				let padding = 6,
					border = 1.5,
					lines = Math.max(
						Math.ceil(
							(this.canvasContext.measureText(this.message).width * 1.2 + padding * 2) / parseInt(this.computedStyles.width)
						),
					1),
					height = border * 2 + padding * 2 + lines * parseInt(this.computedStyles.lineHeight)

				this.textarea.style.height = `${height}px`
			},
			sendMessage(event) {
				if (this.sendingDisabled) {
					return false
				}
				this.disabled = true
				this.error = ''
				this.socket.emit('send-message', {
					room: this.room,
					message: {
						username: this.currentUserFullName,
						content: this.message,
					}
				})
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			setListeners() {
				this.socket.on('message-processed', (data) => {
					this.disabled = false
					if (data.sent) {
						this.message = ''
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
				})
			}
		},
		mounted() {
			this.textarea = document.getElementById(this.inputId)
			this.setTextareaStyles()
			this.setTextMeasureCanvas()
		},
		updated() {
			this.setTextareaStyles()
		},
		watch: {
			'loaded' () {
				if (this.loaded) {
					this.setListeners()
				}
			}
		}
	}

</script>
