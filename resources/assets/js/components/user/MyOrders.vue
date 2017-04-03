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
					<a :href="paymentUrl">Zapisz się na kurs</a>
				</div>
			</div>
		</div>
		<wnl-order :order="order" v-for="order in orders"></wnl-order>
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
				orders: {}
			}
		},
		computed: {
			paymentUrl() {
				return getUrl('payment/select-product')
			},
		},
		methods: {
			getOrders() {
				axios.get(getApiUrl(`orders/all`))
						.then((response) => {
							this.orders = response.data.filter(this.isConfirmed)
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
