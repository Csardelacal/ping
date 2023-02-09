<template>
	<popper trigger="clickToOpen" placement="bottom-end" offset-distance="-30">
		<button class="rounded-full p-1 text-gray-800 hover:bg-sky-50 dark:hover:bg-purple-700 hover:text-sky-400 dark:hover:text-purple-300 active:bg-sky-200 transition-all">
			<EllipsisHorizontalIcon class="w-6 h-6"></EllipsisHorizontalIcon>
		</button>
		<template #content>
			<div ref="menu" class="shadow-md border border-gray-100 dark:border-gray-500 rounded-lg bg-white dark:bg-slate-800 w-80">
				<slot></slot>
			</div>
		</template>
	</popper>
</template>

<style lang="scss" scoped>

</style>

<script lang="ts" setup> 
import Popper from 'vue3-popper';
import { EllipsisHorizontalIcon } from '@heroicons/vue/24/outline';
</script>
