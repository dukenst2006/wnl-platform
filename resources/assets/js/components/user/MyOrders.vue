<template lang="html">
	<div class="container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Twoje zamówienia
				</div>
			</div>
			<div class="level-right">
				<div class="level-item metadata">
					<a :href="paymentUrl">Złóż nowe zamówienie</a>
				</div>
			</div>
		</div>
		<div class="notification is-success strong has-text-centered" v-if="orderSuccess">
			Dziękujemy za złożenie zamówienia!<br>Potwierdzenie znajdziesz na podanym przez siebie adresie e-mail.
		</div>
		<div v-if="loaded">
			<div v-if="hasOrders">
				<wnl-order :order="order" v-for="order in orders" :key="order.id"></wnl-order>
			</div>
			<div v-else>
				<div class="box has-text-centered">
					<p class="title is-5">Brak potwierdzonych zamówień <wnl-emoji name="package"></wnl-emoji></p>
					<p class="has-text-centered">
						<a :href="paymentUrl" class="button is-primary">Zapisz się na kurs</a>
					</p>
				</div>
			</div>
		</div>
		<wnl-text-loader v-else>Wczytuję zamówienia...</wnl-text-loader>
	</div>

</template>

<script>
	import axios from 'axios'
	import _ from 'lodash'
	import {getUrl, getApiUrl, getImageUrl} from 'js/utils/env'
	import Order from './Order'

	export default {
		name: 'MyOrders',
		data () {
			return {
				loaded: false,
				orders: {}
			}
		},
		computed: {
			paymentUrl() {
				return getUrl('payment/select-product')
			},
			hasOrders() {
				return !_.isEmpty(this.orders)
			},
			orderSuccess() {
				return this.$route.query.hasOwnProperty('payment')
			},
		},
		methods: {
			getOrders() {
				axios.get(getApiUrl(`orders/all`))
						.then((response) => {
							if (_.isEmpty(response.data)) {
								this.orders = []
							}

							this.orders = response.data.filter(this.isConfirmed)
							this.loaded = true
						})
						.catch(console.log.bind(console))
			},
			isConfirmed(order) {
				return !_.isEmpty(order.method)
			},
		},
		mounted() {
			this.getOrders()
		},
		components: {
			'wnl-order': Order,
		}
	}
</script>
