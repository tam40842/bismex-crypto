<template>
	<div class="t-grid t-grid-flow-row t-gap-2 t-auto-rows-max t-my-4">
		<div class="t-flex t-justify-between t-items-center">
			<small v-if="!is_desktop" class="t-pl-1 t-text-[#03A593]">
				{{ 100 - progress }}%
			</small>
			<small v-if="is_desktop" class="t-text-[#03A593]">
				{{ 100 - progress }}%
			</small>
			<div class="t-block t-w-[80%]">
				<div
					class="t-h-6 t-rounded-md t-relative t-bg-[#CF304A] t-overflow-hidden"
				>
					<div
						class="t-absolute t-left-0 t-top-0 t-h-6 t-bg-[#03A593]  t-rounded-md t-transition-all t-duration-150"
						:style="{ width: 100 - progress + '%' }"
					></div>
				</div>
			</div>
			<small v-if="is_desktop" class="t-text-[#CF304A]">
				{{ progress }}%
			</small>
			<small v-if="!is_desktop" class="t-pr-1 t-text-[#CF304A]">
				{{ progress }}%
			</small>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		progress: {
			type: Number,
			default: 50,
		},
		is_desktop: {
			type: Boolean,
			default: true,
		},
	},
	filters: {
		intFormat(v) {
			return Number(v).toFixed(0);
		},
	},
};
</script>

<style lang="scss">
#trader_percent {
	.v-progress-linear__determinate.primary {
		background-color: #CF304A !important;
	}
	.v-progress-linear__background {
		transition: width 0.3s ease;
	}
}
</style>
