<template>
	<v-container>
		<div class="t-block">
			<TabsRouter class="lg:t-w-[35%] t-mx-auto t-py-5" :router="router" />
		</div>
		<div class="t-flex-grow">
			<NuxtChild />
		</div>
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
					name: this.$t("autotradePage.overview"),
					path: "/autotrade/overview",
				},
				{
					name: this.$t("autotradePage.history"),
					path: "/autotrade/history",
				},
			];
		},
	},
};
</script>

<style></style>
