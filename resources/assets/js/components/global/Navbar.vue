<template>
	<nav class="wnl-navbar has-shadow">
		<div class="wnl-left">
			<div class="wnl-left-content">
				<!-- <router-link :to="{ name: 'dashboard' }" class="wnl-logo-link">
					<img :src="logoSrc" :alt="logoAlt">
				</router-link> -->
				<a :href="paymentUrl" class="wnl-logo-link">
					<img :src="logoSrc" :alt="logoAlt">
				</a>
			</div>
		</div>
		<div class="wnl-middle"></div>
		<div class="wnl-right">
			<wnl-user-dropdown></wnl-user-dropdown>
		</div>
	</nav>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-navbar
		+small-shadow()
		display: flex
		height: $navbar-height
		z-index: 50

	.wnl-nav-item
		align-items: center
		display: flex
		height: 100%

	.wnl-logo-link
		box-sizing: content-box
		display: block
		max-width: 150px
		padding: $margin-base 0

		img
			display: block

	.wnl-right
		height: 100%
</style>

<script>
	import Dropdown from 'js/components/user/Dropdown.vue'
	import { mapGetters } from 'vuex'
	import { getImageUrl } from 'js/utils/env'

	export default {
		name: 'Navbar',
		computed: {
			...mapGetters([
				'currentUserFullName'
			]),
			paymentUrl() {
				return 'https://wiecejnizlek.pl'
			},
			logoSrc() {
				return getImageUrl('wnl-logo.svg')
			},
			logoAlt() {
				return 'Logo Więcej niż LEK'
			}
		},
		components: {
			'wnl-user-dropdown': Dropdown,
		},
	}
</script>
