<template>
	<div class="t-grid t-grid-cols-1 xl:t-grid-cols-10 t-auto-rows-max t-gap-4">
		<v-form
			@submit.prevent="postWithdraw()"
			class="t-mx-auto xl:t-col-span-4 t-w-full"
			:disabled="
				$auth.user.google2fa_enable === 0 || $auth.user.kyc_status !== 2
			"
		>
			<v-card
				color="#19232A"
				class="lg:t-col-span-1 t-border t-border-custom-03A593 t-p-4"
				width="100%"
			>
				<v-alert type="warning" v-if="$auth.user.google2fa_enable === 0">
					{{ $t("withdrawPage.warningStart") }}
					<br />{{ $t("withdrawPage.submit") }}
					<router-link to="/profile/2fa" tag="a" class="white--text">
						<strong>
							<u>{{ $t("withdrawPage.2fa") }}</u>
						</strong>
					</router-link>
					<br />{{ $t("withdrawPage.warningEnd") }}
				</v-alert>
				<v-alert type="warning" v-if="$auth.user.kyc_status !== 2">
					{{ $t("withdrawPage.warningStart") }}
					<br />{{ $t("withdrawPage.submit") }}
					<router-link to="/profile/kyc" tag="a" class="white--text">
						<strong>
							<u>{{ $t("withdrawPage.kyc") }}</u>
						</strong>
					</router-link>
					<br />{{ $t("withdrawPage.submit") }}
				</v-alert>
				<div class="t-grid t-grid-flow-row t-gap-3 t-auto-rows-max">
					<!-- <v-select solo label="Select Currency" hide-details></v-select> -->
					<v-text-field
						color="#03A593"
						v-model="address"
						label="Wallet address"
						solo
						hide-details
					></v-text-field>
					<v-text-field
						solo
						color="#03A593"
						hide-details
						class="text-right"
						placeholder="0.00"
						v-model="amount"
						value
						append-icon="mdi-currency-usd"
					>
						<template #prepend-inner>
							<span>{{ $t("withdrawPage.amount") }}</span>
						</template>
					</v-text-field>
					<v-text-field
						solo
						color="#03A593"
						hide-details
						class="text-right"
						:value="currencies.withdraw_fee | displayCurrency(2)"
						:disabled="true"
						append-icon="mdi-currency-usd"
						readonly
					>
						<template #prepend-inner>
							<span>{{ $t("withdrawPage.withdrawFee") }}</span>
						</template>
					</v-text-field>
					<v-text-field solo v-model="twofa_code" color="#03A593" hide-details>
						<template #label>
							<span class="sm:t-block t-hidden">{{
								$t("withdrawPage.2faLabel")
							}}</span>
							<small class="sm:t-hidden">{{
								$t("withdrawPage.2faLabel")
							}}</small>
						</template>
					</v-text-field>
				</div>
				<div class="t-grid t-grid-flow-row t-auto-rows-max t-py-5 white--text">
					<div class="t-flex">
						<span class="t-mr-2"
							>{{ $t("withdrawPage.availableBalance") }}:</span
						>
						<span class="notranslate"
							>${{ $auth.user.live_balance | displayCurrency(2) }}</span
						>
					</div>
					<div class="t-flex">
						<span class="t-mr-2">{{ $t("withdrawPage.withdrawFee") }}:</span>
						<span
							>${{ currencies.withdraw_fee | displayCurrency(2) }} USDT</span
						>
					</div>
					<div class="t-flex t-text-xl t-font-bold">
						<span class="t-mr-2">{{ $t("withdrawPage.youWill") }}</span>
						<span
							>${{
								(amount - currencies.withdraw_fee) | displayCurrency(2)
							}}
							USDT</span
						>
					</div>
				</div>
				<v-card-actions>
					<v-btn
						color="#03A593"
						type="submit"
						class="white--text t-w-1/2 t-mx-auto"
						height="46"
						:loading="$load('withdraw')"
						:disabled="
							$auth.user.google2fa_enable === 0 || $auth.user.kyc_status !== 2
						"
						>{{ $t("withdrawPage.withdraw") }}</v-btn
					>
				</v-card-actions>
			</v-card>
		</v-form>
		<div class="xl:t-col-span-6">
			<v-card
				color="#19232A"
				class="t-border t-border-custom-03A593 t-max-w-full t-overflow-x-auto"
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
							<v-btn color="#EBA900" @click="SearchWithdraw" class="t-w-full" elevation="0">Search</v-btn>
						</div>
					</div>
				</v-card-actions> -->
				<div class>
					<v-simple-table class="t-bg-transparent">
						<template v-slot:default>
							<thead>
								<tr class="t-bg-[#00695d] white--text">
									<th class="white--text t-text-center">
										{{ $t("walletPage.date") }}
									</th>
									<th class="white--text t-text-center">
										{{ $t("walletPage.amount") }}
									</th>
									<th class="white--text t-text-center">
										{{ $t("walletPage.total") }}
									</th>
									<th class="white--text t-text-center">
										{{ $t("walletPage.address") }}
									</th>
									<th class="white--text t-text-center">
										{{ $t("walletPage.txHash") }}
									</th>
									<th class="white--text t-text-center">
										{{ $t("walletPage.status") }}
									</th>
								</tr>
							</thead>
							<tbody v-if="histories_withdraw">
								<tr
									v-for="(v, i) in histories_withdraw.data"
									:key="i"
									class="hover:t-bg-custom-03A593 hover:t-bg-opacity-10 t-transition-all t-duration-300"
									:class="[i % 2 ? 't-text-custom-03A593' : 't-text-white']"
								>
									<td class="t-text-center">{{ v.created_at }}</td>
									<td class="t-text-center">
										${{ v.amount | displayCurrency(2) }}
									</td>
									<td class="t-text-center">{{ v.total }}</td>
									<td class="t-text-center">{{ v.output_address }}</td>
									<td class="t-text-center">
										{{ v.txhash ? v.txhash : "---" }}
									</td>
									<td class="t-text-center" v-if="v.status == 0">
										{{ $t("walletPage.pending") }}
									</td>
									<td class="t-text-center" v-else-if="v.status == 1">
										{{ $t("walletPage.completed") }}
									</td>
									<td v-else>{{ $t("walletPage.cancelled") }}</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
				</div>
				<v-card-actions
					class="t-px-4 t-border-t t-border-custom-EFEFEF pt-3 t-border-opacity-20"
				>
					<div class="text-center t-w-full" v-if="histories_withdraw">
						<v-pagination
							v-model="page"
							:value="histories_withdraw.current_page"
							:length="histories_withdraw.last_page"
							@next="
								HistoriesWithdraw({
									page: histories_withdraw.current_page + 1,
									date_from: date_from,
									date_to: date_to,
								})
							"
							@previous="
								HistoriesWithdraw({
									page: histories_withdraw.current_page - 1,
									date_from: date_from,
									date_to: date_to,
								})
							"
							@input="(v) => HistoriesWithdraw(v)"
							total-visible="7"
							color="#03A593"
						></v-pagination>
					</div>
				</v-card-actions>
			</v-card>
		</div>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			items: ["Bitcoin", "Etherum", "USDT"],
			symbol: null,
			address: null,
			amount: null,
			total: 0,
			twofa_code: null,
			page: 1,
			date_popup_from: false,
			date_popup_to: false,
			date_from: null,
			date_to: null,
			page: 1,
		};
	},
	methods: {
		...mapActions("wallet", [
			"CURRENCIES",
			"HISTORIES_WITHDRAW",
			"POST_WITHDRAW",
		]),
		HistoriesWithdraw() {
			const data = {
				page: this.page,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_WITHDRAW(data);
		},
		SearchWithdraw() {
			const data = {
				page: 1,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_WITHDRAW(data);
		},
		total_withdraw(amount) {},
		async postWithdraw() {
			const data = {
				symbol: this.currencies.symbol,
				address: this.address,
				amount: this.amount,
				twofa_code: this.twofa_code,
			};
			await this.POST_WITHDRAW({
				data,
				callback: () => {
					this.address = "";
					this.amount = "";
					this.twofa_code = "";
				},
			});
		},
	},
	async mounted() {
		// if (this.$auth.user.admin_setup) {
		// 	this.$router.push("transfer");
		// 	this.$toasted.error("You are not permission in withdraw.");
		// }
		await this.CURRENCIES();
		await this.HistoriesWithdraw();
	},
	computed: {
		...mapState("wallet", ["histories_withdraw", "currencies"]),
	},
};
</script>

<style lang="scss">
.text-right input {
	text-align: right;
}
</style>
