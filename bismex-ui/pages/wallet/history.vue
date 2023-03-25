<template>
	<v-card
		color="#19232A"
		class="t-border t-border-custom-EFEFEF t-max-w-full t-overflow-x-auto"
		id="history"
	>
		<div class>
			<v-simple-table class="t-bg-transparent">
				<template v-slot:default>
					<thead>
						<tr class="t-bg-custom-1E4968 white--text">
							<th class="white--text t-text-center">Order</th>
							<th class="white--text t-text-center">Date</th>
							<th class="white--text t-text-center">Method</th>
							<th class="white--text t-text-center">Type</th>
							<th class="white--text t-text-center">Status</th>
							<th class="white--text t-text-center">Bonus amount</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(v, i) in table"
							:key="i"
							class="hover:t-bg-custom-EBA900 hover:t-bg-opacity-10 t-transition-all t-duration-300"
							:class="[i % 2 ? 't-text-custom-EBA900' : 't-text-white']"
						>
							<td class="t-text-center">{{ i }}</td>
							<td class="t-text-center">{{ v.date }}</td>
							<td class="t-text-center">{{ v.method }}</td>
							<td class="t-text-center">{{ v.type }}</td>
							<td class="t-text-center">{{ v.status }}</td>
							<td class="t-text-center">{{ v.bonus }}</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table>
		</div>
		<v-card-actions
			class="t-px-4 t-border-t t-border-custom-EFEFEF py-3 t-border-opacity-20 t-bg-custom-1E4968"
		>
			<div class="t-grid 2xl:t-grid-cols-4 md:t-grid-cols-4 t-grid-cols-2 2xl:t-gap-8 t-gap-4">
				<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
					<label for="from" class="white--text">From</label>
					<v-menu
						ref="menu"
						v-model="date_popup_from"
						:close-on-content-click="false"
						:return-value.sync="date_from"
						transition="scale-transition"
						offset-y
						min-width="auto"
					>
						<template v-slot:activator="{ on }">
							<v-text-field
								id="from"
								background-color="#FFF"
								solo
								hide-details
								label="From time"
								v-model="date_from"
								readonly
								v-on="on"
								class="t-cursor-pointer"
							>
								<template #append>
									<v-icon v-on="on">mdi-calendar</v-icon>
								</template>
							</v-text-field>
						</template>
						<v-date-picker v-model="date_from" no-title scrollable color="#EBA900">
							<v-spacer></v-spacer>
							<v-btn text color="primary" @click="date_popup_from = false">Cancel</v-btn>
							<v-btn text color="primary" @click="$refs.menu.save(date)">OK</v-btn>
						</v-date-picker>
					</v-menu>
				</div>
				<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
					<label for="from" class="white--text">To</label>
					<v-menu
						ref="menu"
						v-model="date_popup_to"
						:close-on-content-click="false"
						:return-value.sync="date_to"
						transition="scale-transition"
						offset-y
						min-width="auto"
					>
						<template v-slot:activator="{ on }">
							<v-text-field
								id="from"
								background-color="#FFF"
								solo
								hide-details
								label="To time"
								v-model="date_to"
								readonly
								v-on="on"
								class="t-cursor-pointer"
							>
								<template #append>
									<v-icon v-on="on">mdi-calendar</v-icon>
								</template>
							</v-text-field>
						</template>
						<v-date-picker v-model="date_to" no-title scrollable color="#EBA900" elevation="0">
							<v-spacer></v-spacer>
							<v-btn text color="primary" @click="date_popup_to = false">Cancel</v-btn>
							<v-btn text color="primary" @click="$refs.menu.save(date)">OK</v-btn>
						</v-date-picker>
					</v-menu>
				</div>
				<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
					<label for="symbol" class="white--text">Symbol</label>
					<v-select
						:items="items"
						v-model="symbol"
						label="Select Symbol"
						solo
						hide-details
						append-icon="mdi-chevron-down"
					></v-select>
				</div>
				<div class="t-flex t-items-end t-pl-3">
					<v-btn color="#EBA900" class="t-w-full" elevation="0">Search</v-btn>
				</div>
			</div>
		</v-card-actions>
		<v-card-actions class="t-px-4 t-border-t t-border-custom-EFEFEF pt-3 t-border-opacity-20">
			<div class="text-center t-w-full">
				<v-pagination v-model="page" :length="6" color="#EBA900"></v-pagination>
			</div>
		</v-card-actions>
	</v-card>
</template>

<script>
	export default {
		data() {
			return {
				items: ["Bitcoin", "Etherum", "USDT"],
				symbol: null,
				page: 1,
				date_popup_from: false,
				date_popup_to: false,
				date_from: null,
				date_to: null,
				table: [
					{
						date: "02/02/2021",
						amount: 10000,
						method: "method",
						type: "type",
						status: "status",
						bonus: 1000,
					},
					{
						date: "02/02/2021",
						amount: 10000,
						method: "method",
						type: "type",
						status: "status",
						bonus: 1000,
					},
					{
						date: "02/02/2021",
						amount: 10000,
						method: "method",
						type: "type",
						status: "status",
						bonus: 1000,
					},
					{
						date: "02/02/2021",
						amount: 10000,
						method: "method",
						type: "type",
						status: "status",
						bonus: 1000,
					},
				],
			};
		},
	};
</script>
<style lang="scss">
#history {
	.v-input__slot {
		min-height: 20px;
		box-shadow: none !important;
	}
	.v-input__control {
		min-height: 0;
	}
}
</style>