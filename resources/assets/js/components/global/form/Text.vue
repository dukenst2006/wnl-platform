<template>
	<div class="field">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<input
				type="text"
				class="input"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:placeholder="placeholder || $slots.default[0].text || ''"
				@input="onInput"
				v-model="inputValue">
		</div>

		<span class="help is-danger" v-if="hasErrors" v-for="error in getErrors" v-text="error"></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import { formInput } from 'js/mixins/form-input'

	export default {
		name: 'Text',
		mixins: [formInput],
		props: ['name', 'placeholder'],
		computed: {
			default() {
				return ''
			},
		}
	}
</script>
