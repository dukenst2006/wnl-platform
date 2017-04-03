<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="isActive = !isActive">
			<wnl-avatar></wnl-avatar>
			<span class="icon">
				<i class="fa fa-angle-down"></i>
			</span>
		</div>
		<transition name="fade">
			<div class="box drawer" v-if="isActive">
				<p class="metadata">{{ currentUserFullName }}</p>
				<ul>
					<li v-for="item in items" class="drawer-item">
						<router-link class="drawer-link" :to="{ name: item.route }">{{item.text}}</router-link>
					</li>
				</ul>
			</div>
		</transition>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-dropdown
		height: 100%
		min-height: 100%
		position: relative

	.activator
		align-items: center
		color: $color-inactive-gray
		cursor: pointer
		display: flex
		height: 100%
		justify-content: center
		margin-left: -$margin-small
		min-height: 100%
		padding: 0 $margin-small
		transition: background $transition-length-base

		&:hover
			background-color: $color-background-light-gray
			transition: background $transition-length-base

		&.is-active
			background-color: $color-background-light-gray
			color: $color-gray

		.icon
			margin: 0 $margin-tiny

	.drawer
		right: -20%
		position: absolute
		top: 95%
		z-index: 1000

	.metadata,
	.drawer-item
		padding: $margin-small $margin-base
		text-align: right
		white-space: nowrap

	.drawer-item
		font-size: $font-size-minus-1

		&:last-child
			border: 0

	.drawer-link,
	.drawer-link.is-active
		font-weight: $font-weight-regular
</style>

<script>
	import { set } from 'vue'
	import { mapGetters } from 'vuex'

	export default {
		name: 'Dropdown',
		data() {
			return {
				isActive: false,
			}
		},
		computed: {
			...mapGetters(['currentUserFullName']),
			items() {
				return [
					{
						'text': 'Twoje zamówienia',
						'route': 'my-orders',
					},
					{
						'text': 'Wyloguj się',
						'route': 'logout',
					},
				]
			}
		},
		watch: {
			'$route' (to, from) {
				this.isActive = false
			}
		}
	}
</script>
