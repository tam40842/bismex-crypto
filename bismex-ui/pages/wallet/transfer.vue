<template>
	<div class="t-grid t-grid-cols-1 xl:t-grid-cols-10 t-auto-rows-max t-gap-4">
		<v-form
			@submit.prevent="postTransfer()"
			class="xl:t-col-span-4 t-mx-auto t-w-full"
		>
			<v-card
				color="#19232A"
				class="t-border t-border-custom-EFEFEF t-p-4"
				width="100%"
			>
				<div class="t-grid t-grid-flow-row t-gap-3 t-auto-rows-max">
					<!-- <v-select solo label="Select Currency" hide-details></v-select> -->
					<v-text-field
						color="#03A593"
						v-model="username"
						:label="`${this.$t('walletPage.address')} USDT.BEP20`"
						solo
						hide-details
					></v-text-field>
					<v-text-field
						solo
						color="#03A593"
						hide-details
						class="text-right"
						v-model="amount"
						value
						placeholder="0.00"
						append-icon="mdi-currency-usd"
					>
						<template #prepend-inner>
							<span>{{ $t("walletPage.amount") }}</span>
						</template>
					</v-text-field>
					<!-- <v-text-field
						solo
						color="#EBA900"
						hide-details
						class="text-right"
						value="0"
						append-icon="mdi-currency-usd"
						readonly
					>
						<template #prepend-inner>
							<span>Transfer Fee</span>
						</template>
					</v-text-field>-->
					<v-text-field solo color="#03A593" v-model="twofa_code" hide-details>
						<template #label>
							<span class="sm:t-block t-hidden">{{
								$t("withdrawPage.2faLabel")
							}}</span>
							<small class="sm:t-hidden">{{ $t("walletPage.2faLabel") }}</small>
						</template>
					</v-text-field>
				</div>
				<div class="t-grid t-grid-flow-row t-auto-rows-max t-py-5 white--text">
					<div class="t-flex">
						<span class="t-mr-2"
							>{{ $t("withdrawPage.availableBalance") }}:</span
						>
						<span>${{ $auth.user.primary_balance | displayCurrency(2) }}</span>
					</div>
					<!-- <div class="t-flex">
						<span class="t-mr-2">Transfer Fee:</span>
						<span>40 USDT</span>
					</div>-->
					<!-- <div class="t-flex t-text-xl t-font-bold">
						<span class="t-mr-2">You Will Get:</span>
						<span>${{ amount | displayCurrency(2) }} USDT</span>
					</div>-->
				</div>
				<v-card-actions>
					<v-btn
						color="#03A593"
						type="submit"
						:loading="$load('transfer')"
						class="white--text t-w-1/2 t-mx-auto"
						height="46"
						>{{ $t("transferPage.transfer") }}</v-btn
					>
				</v-card-actions>
			</v-card>
		</v-form>
		<v-card
			color="#19232A"
			class="t-border t-border-custom-EFEFEF t-max-w-full t-overflow-x-auto xl:t-col-span-6"
			id="history"
		>
			<!-- <v-card-actions
				class="t-px-4 t-border-t t-border-custom-EFEFEF py-3 t-border-opacity-20 t-bg-custom-1E4968"
			>
				<div class="t-grid 2xl:t-grid-cols-3 md:t-grid-cols-3 t-grid-cols-2 2xl:t-gap-8 t-gap-4">
					<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
						<label for="from" class="white--text">From</label>
						<v-menu
							ref="menu_from"
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
								<v-btn text color="primary" @click="$refs.menu_from.save(date_from)">OK</v-btn>
							</v-date-picker>
						</v-menu>
					</div>
					<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
						<label for="from" class="white--text">To</label>
						<v-menu
							ref="menu_to"
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
								<v-btn text color="primary" @click="$refs.menu_to.save(date_to)">OK</v-btn>
							</v-date-picker>
						</v-menu>
					</div>
					<div class="t-flex t-items-end t-pl-3">
						<v-btn color="#EBA900" @click="SearchTransfer" class="t-w-full" elevation="0">Search</v-btn>
					</div>
				</div>
			</v-card-actions>-->
			<div class>
				<v-simple-table class="t-bg-transparent">
					<template v-slot:default>
						<thead>
							<tr class="t-bg-custom-03A593 white--text">
								<th class="white--text t-text-center">
									{{ $t("walletPage.date") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("commissionPage.recipient") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("commissionPage.sender") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("walletPage.amount") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("walletPage.totalPrimary") }}
								</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(v, i) in histories_transfer.data"
								:key="i"
								class="hover:t-bg-custom-03A593 hover:t-bg-opacity-10 t-transition-all t-duration-300"
								:class="[i % 2 ? 't-text-custom-03A593' : 't-text-white']"
							>
								<td class="t-text-center">{{ v.created_at }}</td>
								<td class="t-text-center">{{ v.user_address }}</td>
								<td class="t-text-center">{{ v.receiver_address }}</td>
								<td
									class="t-text-center"
									v-if="v.status == $t('commissionPage.sender')"
								>
									-${{ v.amount | displayCurrency(2) }}
								</td>
								<td class="t-text-center" v-else>
									+${{ v.amount | displayCurrency(2) }}
								</td>
								<td
									class="t-text-center"
									v-if="v.status == $t('commissionPage.sender')"
								>
									${{ v.user_balance | displayCurrency(2) }}
								</td>
								<td class="t-text-center" v-else>
									${{ v.recipient_balance | displayCurrency(2) }}
								</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
			</div>

			<v-card-actions
				class="t-px-4 t-border-t t-border-custom-EFEFEF pt-3 t-border-opacity-20"
			>
				<div class="text-center t-w-full">
					<v-pagination
						v-model="page"
						:value="histories_transfer.current_page"
						:length="histories_transfer.last_page"
						@next="
							HistoriesTransfer({
								page: histories_transfer.current_page + 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@previous="
							HistoriesTransfer({
								page: histories_transfer.current_page - 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@input="(v) => HistoriesTransfer(v)"
						total-visible="7"
						color="#03A593"
					></v-pagination>
				</div>
			</v-card-actions>
		</v-card>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
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
			username: null,
			amount: null,
			twofa_code: null,
		};
	},
	methods: {
		...mapActions("wallet", ["HISTORIES_TRANSFER", "POST_TRANSFER"]),
		HistoriesTransfer() {
			const data = {
				page: this.page,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_TRANSFER(data);
		},
		SearchTransfer() {
			const data = {
				page: 1,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_TRANSFER(data);
		},
		async postTransfer() {
			const data = {
				recipient: this.username,
				amount: this.amount,
				twofa_code: this.twofa_code,
			};
			await this.POST_TRANSFER({
				data,
				callback: async () => {
					this.username = "";
					this.amount = "";
					this.twofa_code = "";
					await this.$auth.fetchUser();
					this.HISTORIES_TRANSFER({
						page: 1,
						date_from: this.date_from,
						date_to: this.date_from,
					});
				},
			});
		},
	},
	async mounted() {
		await this.HistoriesTransfer();
	},
	computed: {
		...mapState("wallet", ["histories_transfer"]),
	},
};
</script>

<style lang="scss">
.text-right input {
	text-align: right;
}
</style>
