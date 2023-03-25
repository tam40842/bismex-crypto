<template>
	<v-container
		fluid
		class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 lg:t-p-5 t-p-3 t-mx-auto"
	>
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
					name: this.$t("walletPage.overview"),
					path: "/wallet/overview",
					active: "wallet-overview",
				},
				{
					name: this.$t("walletPage.deposit"),
					path: "/wallet/deposit",
					active: "wallet-deposit",
				},
				{
					name: this.$t("walletPage.withdraw"),
					path: "/wallet/withdraw",
					active: "wallet-withdraw",
				},
				{
					name: this.$t("walletPage.transfer"),
					path: "/wallet/transfer",
					active: "wallet-transfer",
				},
			];
		},
	},
};
</script>

<style></style>
