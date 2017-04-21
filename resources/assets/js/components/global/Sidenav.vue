<template>
	<!-- Breadcrumbs -->
	<div class="wnl-sidenav">

		<ul class="breadcrumbs" v-if="breadcrumbs">
			<wnl-sidenav-item v-for="breadcrumb in breadcrumbs"
							  :itemClass="breadcrumb.itemClass"
							  :to="breadcrumb.to"
							  :isDisabled="breadcrumb.isDisabled"
							  :method="breadcrumb.method"
							  :iconClass="breadcrumb.iconClass"
							  :iconTitle="breadcrumb.iconTitle"
							  :key="breadcrumb.id"
			>
				{{breadcrumb.text}}
			</wnl-sidenav-item>
		</ul>

		<!-- Items -->
		<div class="box">
			<ul class="items" v-if="items">
				<li class="item heading small" v-if="itemsHeading">{{itemsHeading}}</li>
				<wnl-sidenav-item v-for="item in items"
								  :itemClass="item.itemClass"
								  :to="item.to"
								  :isDisabled="item.isDisabled"
								  :method="item.method"
								  :iconClass="item.iconClass"
								  :iconTitle="item.iconTitle"
								  :key="item.id"
				>
					{{item.text}}
				</wnl-sidenav-item>
			</ul>
		</div>
	</div>

</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		max-width: 280px

	.breadcrumbs
		font-size: $font-size-minus-1
		margin-bottom: $margin-base

		.small
			font-size: $font-size-minus-2

		.big
			font-size: $font-size-base

		.is-active
			font-weight: $font-weight-regular

	.items

		.item
			padding: $margin-tiny 0

			&.with-border
				padding: $margin-base 0 $margin-tiny

				&:first-child
					padding: $margin-tiny 0

		.heading
			padding-top: 10px

			&:first-child
				padding: 0

		.small
			font-size: $font-size-minus-1

		.big
			font-size: $font-size-plus-1

		.subitem
			padding: 5px 0 5px 15px

		.disabled
			color: $color-inactive-gray

	.todo
		color: $primary

		.icon
			vertical-align: baseline

	.is-active
		font-weight: $font-weight-bold
</style>

<script>
	import SidenavItem from 'js/components/global/SidenavItem'

	export default {
		props: ['breadcrumbs', 'items', 'itemsHeading'],
		components: {
			'wnl-sidenav-item': SidenavItem,
		}
	}
</script>
