<template>
	<v-container>
		<div class="lg:t-p-5 t-p-3">
			<TabsRouter
				class="lg:t-w-2/5 t-mx-auto t-py-5"
				:router="router"
				v-if="$store.state.shareholder.accept"
			/>
			<NuxtChild />
		</div>
	</v-container>
</template>

<script>
	export default {
		layout: "board",
		data() {
			return {
				router: [
					{
						name: "Overview",
						path: "/shareholder/overview",
						active: "shareholder-ticket",
					},
					{
						name: "History",
						path: "/shareholder/history",
						active: "shareholder-history",
					},
				],
			};
		},
	};
</script>

<style>
</style>