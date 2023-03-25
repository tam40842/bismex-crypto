<template>
	<v-card class="t-bg-transparent t-border-none t-shadow-none">
		<div class="t-grid t-grid-cols-3 t-justify-evenly t-w-full t-gap-16 t-p-2">
			<div
				:class="isPortrait ? 't-gap-1' : 't-gap-2'"
				class="t-grid t-grid-cols-5"
				v-for="(item, index) in blurData"
				:key="index"
			>
				<div class="t-grid t-grid-rows-4 2xl:t-gap-1 t-gap-1" v-for="(i, k) in item" :key="k">
					<div
						class="2xl:t-h-3 2xl:t-w-3 lg:t-h-3 lg:t-w-3 t-rounded-full t-mx-auto t-row-span-1"
						:class="[color(r), isPortrait ? 't-h-4 t-w-4' : ' t-h-3 t-w-3']"
						v-for="(r, t) in i"
						:key="t"
					></div>
				</div>
			</div>
		</div>
	</v-card>
</template>

<script>
	export default {
		data() {
			return {
				isPortrait: true,
			};
		},
		props: {
			blurData: {
				type: Array,
				default: () => [
					[Array(4), Array(4), Array(4), Array(4), Array(4)],
					[Array(4), Array(4), Array(4), Array(4), Array(4)],
					[Array(4), Array(4), Array(4), Array(4), Array(4)],
					[Array(4), Array(4), Array(4), Array(4), Array(4)],
					[Array(4), Array(4), Array(4), Array(4), Array(4)],
				],
			},
		},
		methods: {
			color(data) {
				if (data === "CALL") return "t-bg-custom-CF304A";
				else if (data === "PUT") return "t-bg-custom-03A593";
				else return "grey";
			},
			handleOrientationChange() {
				const orientation = window.screen.orientation.type;
				if (orientation === "portrait-primary") {
					this.isPortrait = true;
				} else if (orientation === "landscape-primary") {
					this.isPortrait = false;
				}
			},
		},
		mounted() {
			window.addEventListener(
				"orientationchange",
				this.handleOrientationChange
			);
		},
	};
</script>

<style></style>
