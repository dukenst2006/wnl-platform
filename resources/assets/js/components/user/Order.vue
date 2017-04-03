<template lang="html">
	<div class="card">
		<div class="card-content">
			<div class="media">
				<div class="media-left">
					<figure class="product-logo image is-48x48">
						<img :src="logoUrl" alt="Logo produktu">
					</figure>
				</div>
				<div class="media-content">
					<p class="title is-4">{{ order.product.name }}</p>
					<p class="subtitle is-6">{{ orderNumber }}</p>
				</div>
			</div>
			<div class="content">
				<p>Metoda płatności: {{ paymentMethod }}</p>
				<div class="transfer-details notification" v-if="transferDetails">
					<p>Dane do przelewu</p>
					<small>
						<strong>Zamówienie numer {{ order.id }}</strong><br>
						bethink sp. z o.o.<br>
						ul. Henryka Sienkiewicza 8/1<br>
						60-817, Poznań<br>
						82 1020 4027 0000 1102 1400 9197 (PKO BP)
					</small>
				</div>
				<small>Zamówienie złożono {{ order.created_at }}</small>
			</div>
		</div>
		<div class="card-footer">
			<div class="card-footer-item payment-status" :class="paymentStatusClass">
				<span class="icon is-small status-icon">
					<i class="fa" :class="iconClass"></i>
				</span>
				{{ paymentStatus }}
			</div>
			<div class="card-footer-item payment-status" :class="paymentStatusClass" v-if="!isPaid">
				<a :href="paymentMethodChangeUrl" title="Zmień metodę płatności">
					<span class="icon is-small status-icon">
						<i class="fa fa-pencil-square-o"></i>
					</span> Zmień metodę płatności
				</a>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/mixins'
	@import 'resources/assets/sass/variables'

	.card
		margin-bottom: 20px

	.product-logo
		+small-shadow()

	.status-icon
		line-height: $line-height-minus
		margin-right: 5px

	.payment-status
		line-height: $line-height-plus

		&.text-success
			color: $color-green

		&.text-warning
			color: $color-yellow

		&.text-info
			color: $color-gray-lighter
</style>

<script>
	import axios from 'axios'
	import {getUrl, getApiUrl, getImageUrl} from 'js/utils/env'
	import {gaEvent} from 'js/utils/tracking'

	export default {
		name: 'Order',
		props: ['order', 'loaderVisible'],
		data() {
			return {
				paymentMethods: {
					'transfer': 'Przelew bankowy',
					'online': 'Szybki przelew',
				},
			}
		},
		computed: {
			loaderSrc() {
				return getImageUrl('loader.svg')
			},
			logoUrl() {
				// TODO: Mar 28, 2017 - Make it dynamic when more courses are added
				return getImageUrl('wnl-logo-square@2x.png')
			},
			isPaid() {
				return this.order.paid
			},
			isPending() {
				// show loader only if there is an online payment waiting for confirmation
				return !this.order.paid && this.order.method === 'online';
			},
			iconClass() {
				if (!this.order.paid && this.order.method === 'online') {
					// Loader
					return 'fa-circle-o-notch fa-spin'
				} else if (this.order.paid) {
					return 'fa-check-circle-o'
				}

				return 'fa-info-circle'
			},
			transferDetails() {
				return !this.order.paid && this.order.method === 'transfer';
			},
			paymentStatus() {
				return this.order.paid ? 'Zapłacono!' : 'Oczekuje na zaksięgowanie'
			},
			paymentStatusClass() {
				if (this.order.cancelled) {
					return 'text-warning'
				} else if (this.order.paid) {
					return 'text-success'
				}

				return 'text-info'
			},
			paymentMethod() {
				return this.paymentMethods[this.order.method]
			},
			paymentMethodChangeUrl() {
				return getUrl('payment/confirm-order')
			},
			orderNumber() {
				return `Zamówienie numer ${this.order.id}`
			}
		},
		methods: {
			checkStatus() {
				axios.get(getApiUrl(`orders/${this.order.id}`))
						.then((response) => {
							if (response.data.paid) {
								this.order.paid = true
							} else {
								setTimeout(this.checkStatus, 5000)
							}
						})
						.catch(console.log.bind(console))
			}
		},
		mounted() {
			if (this.isPending) this.checkStatus()
			if (this.$route.query.hasOwnProperty('payment')) {
				gaEvent('Payment', this.order.method)
			}
		}
	}
</script>
