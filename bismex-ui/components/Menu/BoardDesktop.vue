<template>
	<div
		class="
			t-w-28
			asidebg
			t-h-full
			xl:t-flex
			t-hidden t-flex-col t-justify-between t-overflow-y-auto
		"
	>
		<v-list-item-group>
			<v-list-item v-for="(v, i) in menu" :key="i" :to="v.path">
				<v-list-item-content class="t-flex-col t-items-center">
					<!-- <img :src="v.icon" :alt="v.name" /> -->
					<i class="icon t-text-2xl" :class="'icon-'+v.icon"></i>
					<v-list-item-title v-text="v.name" class="t-capitalize t-text-sm t-max-w-full"></v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list-item-group>
		<v-spacer></v-spacer>
		<v-list-item class="t-max-h-24" v-on:click="$auth.logout()">
			<v-list-item-content class="t-flex-col t-items-center">
				<!-- <component :is="'logoutIcon'"></component> -->
				<v-list-item-icon class="t-m-auto">
					<i class="icon icon-logout t-text-2xl"></i>
				</v-list-item-icon>
				<v-list-item-title class="t-capitalize t-text-sm">{{
					$t("logout")
				}}</v-list-item-title>
			</v-list-item-content>
		</v-list-item>
	</div>
</template>

<script>
export default {
	data() {
		return {
		};
	},
	computed: {
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
};
</script>

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
