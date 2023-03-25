<template>
	<div v-if="router.length > 0" class="t-text-center">
		<v-tabs
			background-color="#19232A"
			color="#03A593"
			class="tabs_router rounded-pill t-overflow-hidden t-w-auto"
			height="35"
			v-model="tab"
		>
			<v-tab
				class="white--text t-px-0 t-w-full"
				v-for="(item, index) in router"
				:key="index"
				:to="item.path"
				><span class="t-z-0 capitalize">{{ item.name }}</span>
			</v-tab>
		</v-tabs>
	</div>
</template>

<script>
export default {
	props: {
		router: {
			type: Array,
			required: true,
			default: [],
		},
	},
	data() {
		return {
			tab: 0,
		};
	},
};
</script>

<style lang="scss">
.tabs_router {
	.v-tab {
		@apply t-min-w-0 t-text-xs;
		@screen sm {
			@apply t-text-base;
		}
	}
	.v-slide-group__content {
		@apply t-grid t-grid-flow-col t-auto-cols-fr;
		> div {
			@apply t-rounded-full t-overflow-hidden;
		}
	}
	.v-tabs-slider-wrapper {
		@apply t-h-full t-rounded-full t-z-0 #{!important};
	}
	.v-ripple__container,
	.v-tab::before {
		@apply t-rounded-full;
	}
}
</style>
