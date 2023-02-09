<template>
	<Popper placement="bottom-end" offset-distance="12" offset-skid="12">
		<button class="align-middle px-2 py-1">
		<img :src="store.state.config.user.avatar" class="w-8 h-8 rounded-full" />
		</button>
		<template #content>
		<div
			class="shadow-md border border-gray-100 dark:border-gray-500 rounded-lg bg-white dark:bg-slate-800 w-80 text-lg"
		>
		
			<ContextItem href="/settings" class="flex items-center text-gray-800">
				<Cog6ToothIcon class="h-6 w-6 mr-4 text-gray-600"></Cog6ToothIcon> Settings
			</ContextItem>
			<ContextItem :href="`/@${store.state.config.user.name}`" class="flex items-center text-gray-800">
				<UserCircleIcon class="h-6 w-6 mr-4 text-gray-600"></UserCircleIcon> My profile
			</ContextItem>
			<ContextItem href="/account/logout" class="flex items-center text-gray-800">
				<ArrowLeftOnRectangleIcon class="h-6 w-6 mr-4 text-gray-600"></ArrowLeftOnRectangleIcon> Logout
			</ContextItem>
		</div>
		</template>
	</Popper>
</template>

<style lang="scss" scoped>
</style>

<script lang="ts" setup>
import Popper from 'vue3-popper';
import ContextItem from '../contextual/item.vue';
import {BuildingStorefrontIcon, ArrowLeftOnRectangleIcon, Cog6ToothIcon, UserCircleIcon} from '@heroicons/vue/24/outline'
import {useStore} from 'vuex';

const store = useStore();
</script>
