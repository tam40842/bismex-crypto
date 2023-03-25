<template>
	<v-navigation-drawer
		v-model="isDrawer"
		absolute
		temporary
		color="asidebg"
		class="xl:t-hidden t-z-40"
	>
		<v-list nav dense class="t-overflow-y-auto">
			<v-list-item class="t-py-5 t-px-0">
				<div
					class="
						t-grid
						t-grid-flow-col
						t-auto-cols-max
						t-gap-4
						t-items-center
						t-w-full
					"
				>
					<v-avatar size="40" color="white" class="t-mx-auto t-cursor-pointer">
						<v-img
							:src="
								$auth.user.avatar
									? $config.apiUrl + $auth.user.avatar
									: $image.avatar_default
							"
							contain
							height="40"
							@click="$router.push('/profile')"
						></v-img>
					</v-avatar>
					<span
						class="t-text-custom-03A593 t-font-bold"
						@click="$router.push('/profile')"
						>{{ $auth.user.username }}</span
					>
				</div>
			</v-list-item>
			<v-list-item-group>
				<v-list-item v-for="(v, i) in menu" :key="i" :to="v.path">
					<v-list-item-icon>
						<i class="icon" :class="'icon-' + v.icon"></i>
					</v-list-item-icon>
					<v-list-item-title
						v-text="v.name"
						class="t-capitalize t-text-sm"
					></v-list-item-title>
				</v-list-item>
			</v-list-item-group>
			<v-spacer></v-spacer>
			<v-list-item class="t-max-h-24" v-on:click="$auth.logout()">
				<!-- <component :is="'logoutIcon'"></component> -->
				<v-list-item-icon>
					<i class="icon icon-logout"></i>
				</v-list-item-icon>
				<v-list-item-title class="t-capitalize t-text-sm">{{
					$t("logout")
				}}</v-list-item-title>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
export default {
	props: {
		drawer: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {};
	},
	computed: {
		isDrawer: {
			get() {
				return this.drawer;
			},
			set(v) {
				this.toggle(v);
			},
		},
		menu() {
			return [
				{
					name: this.$t("trading"),
					path: "/trading",
					icon: "trading",
				},
				{
					name: this.$t("commission"),
					path: "/commission",
					icon: "commission",
				},
				{
					name: this.$t("wallet"),
					path: "/wallet",
					icon: "wallet",
				},
				{
					name: this.$t("member"),
					path: "/member",
					icon: "network",
				},
				{
					name: this.$t("profile"),
					path: "/profile",
					icon: "profile",
				},
				{
					name: this.$t("support"),
					path: "/support",
					icon: "support",
				},
				{
					name: this.$t("autoTrade"),
					path: "/autotrade/overview",
					icon: "money",
				},
			];
		},
	},
	methods: {
		toggle(v) {
			this.$emit("toggle", v);
		},
	},
};
</script>

<style scoped>
.link {
	background-color: #0e2f4a;
}
</style>
<style lang="scss" scoped>
.v-list-item:not(.v-list-item--active) {
	* {
		color: #fff;
	}
}
.v-list-item--active {
	// background-image: linear-gradient(
	// 	294.25deg,
	// 	#0bb577 -1.22%,
	// 	#aff9f4 101.99%
	// );
	* {
		color: #03a593;
	}
}
</style>
