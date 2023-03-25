<template>
	<div class="t-grid t-grid-cols-1 xl:t-grid-cols-4 t-gap-4">
		<div class="t-block">
			<v-card
				class="t-border t-border-custom-EFEFEF t-p-4 v-card-linear t-flex t-flex-col t-items-center"
			>
				<v-card-subtitle class="t-text-gray-500 t-pb-0">Shareholder Wallet</v-card-subtitle>
				<v-card-title class="white--text t-font-bold t-pt-0">$100.000.00</v-card-title>
				<div class="t-block">
					<v-img :src="$image.line_white"></v-img>
				</div>
				<div class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 t-mt-6">
					<div class="t-border t-border-custom-EFEFEF t-overflow-visible t-rounded">
						<v-btn
							width="144"
							color="#032545"
							class="t-text-custom-EBA900"
							active-class="t-bg-custom-EBA900 white--text"
							elevation="0"
							to="/shareholder/overview/pack"
						>Upgrade</v-btn>
					</div>
					<div class="t-border t-border-custom-EFEFEF t-overflow-visible t-rounded">
						<v-btn
							width="144"
							color="#032545"
							class="t-text-custom-EBA900"
							active-class="t-bg-custom-EBA900 white--text"
							elevation="0"
							to="/shareholder/overview/deposit"
						>Deposit</v-btn>
					</div>
					<div class="t-border t-border-custom-EFEFEF t-overflow-visible t-rounded">
						<v-btn
							width="144"
							color="#032545"
							class="t-text-custom-EBA900"
							active-class="t-bg-custom-EBA900 white--text"
							elevation="0"
							to="/shareholder/overview/withdraw"
						>Withdraw</v-btn>
					</div>
				</div>
				<div class="xl:t-h-24"></div>
			</v-card>
		</div>
		<div class="t-block xl:t-col-span-3">
			<NuxtChild />
		</div>
	</div>
</template>

<script>
	export default {
		beforeRouteEnter(to, from, next) {
			next((vm) => {
				if (!vm.$store.state.shareholder.accept) next("/shareholder/join");
			});
		},
	};
</script>

<style>
</style>