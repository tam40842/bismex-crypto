<template>
	<div class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 lg:t-p-5 t-p-3 t-mx-auto t-w-full">
		<v-card color="#19232A" class="t-border t-border-custom-EFEFEF t-p-4">
			<v-card-text class="t-grid lg:t-grid-cols-4 t-grid-cols-2 t-gap-4">
				<v-sheet
					color="#FFF7EB"
					class="t-rounded t-text-center t-grid t-grid-flow-row t-auto-rows-max t-px-4 t-py-5"
					v-for="i in 8"
					:key="i"
				>
					<v-card-title class="t-block t-py-0">Level {{ i }}</v-card-title>
					<v-card-subtile class="t-py-0 t-font-bold">Get 1,2% F{{ i }}</v-card-subtile>
					<div class="t-h-2 t-w-2/3 t-rounded-full t-bg-custom-EBA900 t-mx-auto"></div>
					<v-card-subtile class="t-py-0 t-font-bold">2 Active F{{ i }}</v-card-subtile>
					<v-card-subtile class="t-py-0 t-text-xs">Volume $500/Week</v-card-subtile>
				</v-sheet>
			</v-card-text>
		</v-card>
		<v-card color="#19232A" class="t-border t-border-custom-EFEFEF">
			<v-simple-table class="t-bg-transparent">
				<template v-slot:default>
					<thead>
						<tr class="t-bg-custom-1E4968 white--text">
							<th class="white--text t-text-center">F</th>
							<th class="white--text t-text-center">No.F1</th>
							<th class="white--text t-text-center">Branch Volume</th>
							<th class="white--text t-text-center">Percent Volume</th>
							<th class="white--text t-text-center">Commission</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(v, i) in 8"
							:key="i"
							class="hover:t-bg-custom-EBA900 hover:t-bg-opacity-10 t-transition-all t-duration-300 white--text"
						>
							<td class="t-text-center">F{{ i + 1 }}</td>
							<td class="t-text-center">0</td>
							<td class="t-text-center">${{ 0 | displayCurrency }}</td>
							<td class="t-text-center">1.2%</td>
							<td class="t-text-center">0</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table>
		</v-card>
	</div>
</template>

<script>
	export default {};
</script>

<style>
</style>
