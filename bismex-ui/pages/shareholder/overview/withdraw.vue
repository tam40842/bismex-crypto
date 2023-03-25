<template>
	<v-card color="#19232A" class="t-border t-border-custom-EFEFEF t-p-4">
		<v-form class="t-grid t-grid-flow-row t-auto-rows-max t-gap-5 lg:t-w-2/3 t-mx-auto">
			<h3 class="t-text-custom-EBA900 t-font-bold t-text-xl text-center t-uppercase t-mb-5">withdraw</h3>
			<v-select solo label="Select Currency" hide-details color="green darken-2"></v-select>
			<v-text-field
				solo
				hide-details
				class="text-right"
				value="0"
				append-icon="mdi-currency-usd"
				color="green darken-2"
				type="number"
			>
				<template #prepend-inner>
					<span>Amount</span>
				</template>
			</v-text-field>
			<v-text-field solo hide-details color="green darken-2">
				<template #label>
					<span class="sm:t-block t-hidden">Two factor authentication</span>
					<small class="sm:t-hidden">Two factor authentication</small>
				</template>
			</v-text-field>
			<v-btn color="#EBA900" class="white--text t-mx-auto" width="140" height="46">submit</v-btn>
		</v-form>
	</v-card>
</template>

<script>
	export default {};
</script>

<style>
</style>