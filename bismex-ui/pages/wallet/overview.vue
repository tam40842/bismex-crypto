<template>
	<div class="t-block manager-table">
		<v-card
			color="#07131C80"
			class="t-border t-rounded t-border-[#03A593] xl:t-w-3/5 t-mx-auto t-my-4"
		>
			<v-card-text class="t-flex t-justify-between t-items-center">
				<div class="t-flex t-flex-col t-border-red-500">
					<span
						class="t-text-sm t-text-white t-text-opacity-70 t-text-center"
						>{{ $t("walletPage.liveBalance") }}</span
					>
					<span
						class="
							notranslate
							t-text-lg
							md:t-text-2xl
							t-text-white t-text-center
						"
						>${{ $auth.user.live_balance | displayCurrency(2) }}</span
					>
					<div
						class="t-flex t-items-center t-justify-center t-gap-2 t-py-4"
						style="min-height: 64px"
					>
						<v-btn
							color="#03A593"
							class="white--text"
							x-small
							fab
							@click="
								(to_balance = 'live_balance'),
									(from_balance = 'primary_balance'),
									(dialog = true)
							"
						>
							<v-icon>mdi-plus</v-icon>
						</v-btn>
						<v-btn
							color="#03A593"
							class="white--text"
							x-small
							fab
							@click="
								(from_balance = 'live_balance'),
									(to_balance = 'primary_balance'),
									(dialog = true)
							"
						>
							<v-icon>mdi-minus</v-icon>
						</v-btn>
					</div>
				</div>
				<div
					class="
						t-flex t-flex-col t-px-[0px]
						md:t-px-[80px]
						t-border-l-2 t-border-r-2 t-border-gray-700
					"
				>
					<span class="t-text-sm t-text-white t-text-opacity-70 t-text-center">
						{{ $t("walletPage.autotradeBalance") }}
					</span>
					<span
						class="
							notranslate
							t-text-lg
							md:t-text-2xl
							t-text-white t-text-center
						"
						>${{ $auth.user.autotrade_balance | displayCurrency(2) }}</span
					>
					<div
						class="t-flex t-items-center t-justify-center t-gap-2 t-py-4"
						style="min-height: 64px"
					>
						<v-btn
							color="#03A593"
							class="white--text"
							x-small
							fab
							@click="
								(to_balance = 'autotrade_balance'),
									(from_balance = 'primary_balance'),
									(dialog = true)
							"
						>
							<v-icon>mdi-plus</v-icon>
						</v-btn>
						<v-btn
							color="#03A593"
							class="white--text"
							x-small
							fab
							@click="
								(from_balance = 'autotrade_balance'),
									(to_balance = 'primary_balance'),
									(dialog = true)
							"
						>
							<v-icon>mdi-minus</v-icon>
						</v-btn>
					</div>
				</div>
				<div class="t-flex t-flex-col">
					<span class="t-text-sm t-text-white t-text-opacity-70 t-text-center">
						{{ $t("walletPage.primaryBalance") }}
					</span>
					<span
						class="
							notranslate
							t-text-lg
							md:t-text-2xl
							t-text-white t-text-center
						"
						>${{ $auth.user.primary_balance | displayCurrency(2) }}</span
					>
					<div
						class="t-flex t-items-center t-justify-center t-gap-2 t-py-4"
						style="min-height: 64px"
					></div>
				</div>
			</v-card-text>
		</v-card>
		<v-dialog v-model="dialog" width="350">
			<v-form @submit.prevent="Overview">
				<v-card color="#00000050">
					<v-card-title class="white--text t-block t-text-center">
						Convert to
						<span v-if="to_balance == 'autotrade_balance'">
							{{ $t("walletPage.autotradeBalance") }}
						</span>
						<span v-else-if="to_balance == 'live_balance'">
							{{ $t("walletPage.liveBalance") }}
						</span>
						<span v-else>
							{{ $t("walletPage.primaryBalance") }}
						</span>
					</v-card-title>
					<v-card-text>
						<v-text-field
							color="#03A593"
							solo
							v-model="amount"
							hide-details
							placeholder="Amount"
							append-icon="mdi-currency-usd"
						></v-text-field>
					</v-card-text>
					<v-card-text>
						<v-text-field
							color="#03A593"
							solo
							v-model="twofa_code"
							hide-details
							placeholder="Two-Factor Authentication"
						></v-text-field>
					</v-card-text>
					<v-card-actions class="t-w-full t-text-center">
						<v-btn
							color="#03A593"
							:loading="$load('overview')"
							type="submit"
							width="150"
							class="t-mx-auto white--text"
							>{{ $t("walletPage.submit") }}</v-btn
						>
					</v-card-actions>
				</v-card>
			</v-form>
		</v-dialog>
		<v-data-table
			:headers="headers"
			:items="histories_transactions.data"
			item-key="text"
			class="elevation-1"
			hide-default-footer
			id="history"
		>
			<template v-slot:item.change="{ item }">
				<div class="t-text-center" v-if="item.change < 0">
					-${{ item.change | displayCurrency(2) }}
				</div>
				<div class="t-text-center" v-else>
					+${{ item.change | displayCurrency(2) }}
				</div>
			</template>
			<template v-slot:footer>
				<div class="d-flex flex-row align-center t-fl t-bg-custom-03A593 t-p-4">
					<v-row align="center">
						<v-col
							cols="12"
							md="12"
							class="t-flex t-justify-center t-items-center"
						>
							<v-pagination
								v-model="page"
								:value="histories_transactions.current_page"
								:length="histories_transactions.last_page"
								@next="
									historiesTransactions({
										page: histories_transactions.current_page + 1,
									})
								"
								@previous="
									historiesTransactions({
										page: histories_transactions.current_page - 1,
									})
								"
								@input="(v) => historiesTransactions(v)"
								total-visible="7"
								color="#03A593"
							></v-pagination>
						</v-col>
					</v-row>
				</div>
			</template>
		</v-data-table>
	</div>
</template>

<script>
// histories_transactions.data
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			from_balance: "live_balance",
			to_balance: "primary_balance",
			dialog: false,
			page: 1,
			amount: null,
			twofa_code: null,
			headers: [
				{ text: this.$t("walletPage.balanceChanges"), value: "change" },
				{ text: this.$t("walletPage.totalBalance"), value: "balance" },
				{ text: this.$t("walletPage.nameModule"), value: "type" },
				{ text: this.$t("walletPage.date"), value: "created_at" },
			],
		};
	},
	methods: {
		...mapActions("wallet", ["OVERVIEW", "TRANSACTION"]),
		historiesTransactions() {
			const data = {
				page: this.page,
			};
			this.TRANSACTION(data);
		},
		Overview() {
			const data = {
				from_balance: this.from_balance,
				to_balance: this.to_balance,
				amount: this.amount,
				twofa_code: this.twofa_code,
			};
			this.OVERVIEW({
				data,
				callback: () => {
					this.amount = null;
					this.twofa_code = null;
					this.dialog = false;
					this.$auth.fetchUser();
					this.historiesTransactions();
				},
			});
		},
	},
	async mounted() {
		await this.historiesTransactions();
	},
	computed: {
		...mapState("wallet", [
			"histories_transactions",
			"currencies",
			"commission_transaction",
		]),
	},
};
</script>

<style></style>
