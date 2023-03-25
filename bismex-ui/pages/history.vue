<template>
	<v-cointainer
		class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 lg:t-p-5 t-p-3 2xl:t-w-3/4 t-mx-auto"
	>
		<div class="t-block">
			<TabsRouter class="lg:t-w-2/5 t-mx-auto t-py-5" :router="router" />
		</div>
		<NuxtChild />
	</v-cointainer>
</template>

<script>
export default {
	layout: "board",
	data() {
		return {
			router: [
				{
					name: "Overview",
					path: "/history/overview",
					active: "history-overview",
				},
				{
					name: "History",
					path: "/history/list",
					active: "history-list",
				},
			],
		};
	},
};
</script>

<style></style>
