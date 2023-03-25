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
					name: this.$t("supportPage.sentTicket"),
					path: "/support/ticket",
					active: "ticket",
				},
				{
					name: this.$t("supportPage.history"),
					path: "/support/history",
					active: "ticket-history",
				},
			];
		},
	},
};
</script>
