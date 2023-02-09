<template>
	<component 
		:is="this.to? 'router-link' : 'a'" 
		class="w-full block px-6 py-4 bg-white dark:text-white dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800 transition-all first:rounded-t-lg last:rounded-b-lg" 
		:to="to"
		:href="href">
		<slot></slot>
	</component>
</template>

<style lang="scss" scoped>

</style>

<script>
export default {
	setup() {
		
	},
	props: [
		'href',
		'to',
	]
}
</script>
