<template>
	<div class="wnl-chat">
		<wnl-users-widget :users="users"></wnl-users-widget>
		<div class="wnl-chat-messages">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside" v-if="loaded">
					<wnl-message v-for="(message, index) in messages"
								 :showAuthor="isAuthorUnique[index]"
								 :username="message.username"
								 :time="message.time"
								 :key="message.id">
							{{ message.content }}
					</wnl-message>
				</div>
				<wnl-text-loader v-else>Ładuję wiadomości...</wnl-text-loader>
			</div>
		</div>
		<div class="wnl-chat-form">
			<wnl-message-form :loaded="loaded" :socket="socket" :room="room" :inputId="inputId"></wnl-message-form>
		</div>
	</div>
</template>
<style lang="sass">
	@import '../../../sass/variables'

	.wnl-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		padding-right: 20px

	.wnl-chat-messages
		display: flex
		flex: 1 1 auto
		flex-direction: column-reverse
		overflow-y: auto

	.wnl-chat-form
		border-top: $border-light-gray
		margin: 20px 0
		padding-top: 20px
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import UsersWidget from '../global/UsersWidget.vue'
	import * as socket from '../../socket'
	import { nextTick } from 'vue'

	export default {
		props: ['room'],
		data() {
			return {
				loaded: false,
				messages: [],
				users: [],
				socket: {}
			}
		},
		computed: {
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous = index - 1,
						halfHourInMs = 1000 * 60 * 30
					return message.username !== this.messages[previous].username ||
						message.time - this.messages[previous].time > halfHourInMs
				})
			},
			container() {
				return this.$el.getElementsByClassName('wnl-chat-messages')[0]
			},
			content() {
				return this.$el.getElementsByClassName('wnl-chat-content')[0]
			},
			contentInside() {
				// In case you wonder - thank Firefox :/
				return this.$el.getElementsByClassName('wnl-chat-content-inside')[0]
			},
			inputId() {
				return `wnl-chat-form-${this.room}`
			},
			input() {
				return document.getElementById(this.inputId)
			}
		},
		methods: {
			joinRoom() {
				this.socket.emit('join-room', {
					room: this.room
				})
				this.socket.on('join-room-success', (data) => {
					if (!this.loaded) {
						this.messages = data.messages
						this.users    = data.users
						this.loaded   = true
						nextTick(() => {
							this.scrollToBottom()
						})
					}
				})
				return true
			},
			changeRoom(oldRoom) {
				this.loaded = false
				this.socket.emit('leave-room', {
					room: oldRoom
				})
				this.joinRoom()
			},
			setListeners(socket) {
				socket.on('user-sent-message', (data) => {
					this.addMessage(data.message)
				})

				socket.on('message-processed', (data) => {
					if (data.sent) {
						this.addMessage(data.message)
					}
				})

				socket.on('error', (data) => {
					console.log(`Socket error: ${data}`)
				})
			},
			addMessage(message) {
				this.messages.push(message)
				nextTick(() => {
					this.scrollToBottom()
					this.input.focus()
				})
			},
			scrollToBottom() {
				this.container.scrollTop = this.contentInside.offsetHeight
			}
		},
		created() {
			socket.connect().then((socket) => {
				this.socket = socket
				this.joinRoom()
				this.setListeners(this.socket)
			}).catch(console.log.bind(console))
		},
		beforeDestroy() {
			socket.disconnect().then(() => {
				return true
			}).catch(console.log.bind(console))
		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm,
			'wnl-users-widget': UsersWidget,
		},
		watch: {
			'room' (newRoom, oldRoom) {
				if (newRoom !== oldRoom) {
					this.changeRoom(oldRoom)
				}
			}
		}
	}

</script>
