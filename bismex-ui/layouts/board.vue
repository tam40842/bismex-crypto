<template>
	<client-only>
		<v-app :style="{'height': $store.state.settings.height}" id="devices">
			<HeaderBoard @open="open(true)" />
			<v-main class="t-pt-16 t-h-full t-overflow-hidden">
				<div class="t-flex t-h-full">
					<MenuBoardDesktop />
					<MenuBoardMobile :drawer="drawer" @toggle="open" />
					<div
						class="t-flex-grow t-h-full"
						:class="{'t-overflow-y-auto': $store.state.settings.scroll}"
						id="board"
					>
						<Nuxt />
					</div>
				</div>
			</v-main>
		</v-app>
	</client-only>
</template>

<script>
	import { mapActions, mapMutations } from "vuex";
	export default {
		data() {
			return {
				height: null,
				drawer: false,
				devices: null,
			};
		},
		methods: {
			...mapMutations("settings", [
				"SET_HEIGHT",
				"SET_IOS",
				"SET_ORIENTATION",
				"SET_SCROLL",
			]),
			open(v) {
				this.drawer = v;
			},
		},
		mounted() {
			let vm = this;

			this.$nextTick(() => {
				vm.devices = navigator.platform;

				vm.SET_ORIENTATION(
					window.matchMedia("(orientation: portrait)").matches || false
				);
				vm.SET_IOS(
					[
						"iPad Simulator",
						"iPhone Simulator",
						"iPod Simulator",
						"iPad",
						"iPhone",
					].includes(navigator.platform)
				);
				vm.SET_HEIGHT(`${window.innerHeight}px`);

				window.addEventListener("resize", () => {
					vm.SET_HEIGHT(`${window.innerHeight}px`);
					vm.SET_ORIENTATION(
						window.matchMedia("(orientation: portrait)").matches ||
							false
					);
				});
			});
		},
	};
</script>

<style lang="scss" scoped>
html {
	overflow: hidden;
}
</style>
<style lang="scss">
#devices .v-application--wrap {
	min-height: 100%;
}
</style>