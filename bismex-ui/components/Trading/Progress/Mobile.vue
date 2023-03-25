<template>
	<div class="t-w-full t-flex t-flex-row t-items-center">
		<span class="t-inline-block t-text-xs t-text-custom-2BB99F">{{progress}}%</span>
		<div class="t-flex-grow">
			<div class="t-w-full t-relative t-rounded-full t-h-1 t-duration-500 t-bg-custom-CF304A">
				<div
					class="t-absolute t-top-0 t-left-0 t-h-1 t-bg-custom-03A593 t-transition-all t-duration-500"
					:style="{width: progress + '%'}"
				></div>
				<div
					class="t-absolute t-w-3 t-h-3 t-transform t-translate-y-1/2 t--top-4 t-transition-all t-duration-500"
					:style="{'left': progress + '%'}"
				>
					<div class="t-relative t-w-3 t-h-3">
						<span
							class="t-absolute t-animate-ping t-inline-flex t-h-3 t-w-3 t-rounded-full t-opacity-20 t-top-1/2"
							:class="color"
						></span>
						<span class="t-relative t-inline-flex t-rounded-full t-h-3 t-w-3 t-bg-white"></span>
					</div>
				</div>
			</div>
		</div>
		<span class="t-inline-block t-text-xs t-text-custom-CF304A">{{100 - progress}}%</span>
	</div>
</template>

<script>
	export default {
		props: {
			progress: {
				type: Number,
				default: 50,
			},
		},
		computed: {
			color() {
				if (this.progress <= 50) return "t-bg-custom-2BB99F";
				else return "t-bg-custom-CF304A";
			},
		},
	};
</script>

<style lang="scss" scoped>
#progress {
	background-image: linear-gradient(
		to right,
		#2bb99f 0%,
		#2bb99f 49%,
		#b6aa00 50%,
		#cf304a 54%,
		#cf304a
	);
}
</style>