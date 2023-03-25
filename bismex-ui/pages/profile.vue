<template>
	<v-container>
		<div class="t-block">
			<TabsRouter class="lg:t-w-2/5 t-mx-auto t-py-5" :router="router" />
		</div>
		<NuxtChild />
	</v-container>
</template>

<script>
export default {
	layout: "board",
	data() {
		return {};
	},
	computed: {
		router() {
			return [
				{
					name: this.$t("profilePage.information"),
					path: "/profile/infomation",
					active: "profile-infomation",
				},
				{
					name: this.$t("profilePage.2fa"),
					path: "/profile/2fa",
					active: "profile-2fa",
				},
				{
					name: this.$t("profilePage.kyc"),
					path: "/profile/kyc",
					active: "profile-kyc",
				},
			];
		},
	},
};
</script>

<style></style>
