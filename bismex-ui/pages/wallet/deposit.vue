<template>
	<div class="t-grid xl:t-grid-cols-10 t-grid-cols-1 t-auto-rows-max t-gap-4">
		<div class="xl:t-col-span-4 t-grid t-grid-flow-row t-auto-rows-max t-gap-4">
			<v-card
				color="#19232A"
				class="lg:t-col-span-1 t-border t-border-custom-03A593 t-p-4"
			>
				<div class="t-flex t-flex-col t-justify-center t-items-center">
					<small class="grey--text darken-2">{{
						$t("walletPage.primaryBalance")
					}}</small>
					<span class="t-text-2xl t-font-bold white--text notranslate">
						${{ $auth.user.primary_balance | displayCurrency(2) }}
					</span>
				</div>
			</v-card>
			<v-card
				color="#19232A"
				class="lg:t-col-span-1 t-border t-border-custom-03A593 t-p-4"
			>
				<div class="t-grid sm:t-grid-cols-3 t-gap-4 t-mx-auto t-items-end">
					<div class="sm:t-col-span-2">
						<div class="t-flex t-flex-col t-justify-between t-h-full">
							<h2 class="white--text t-font-bold t-text-center">
								{{ currencies.name }}
							</h2>
							<!-- <v-img :src="currencies.logo" class="t-w-1/3 t-mx-auto t-my-3"></v-img> -->
							<div class="t-flex t-flex-col">
								<span class="white--text sm:t-text-left t-text-center t-mb-1">{{
									$t("walletPage.copyAddress")
								}}</span>
								<v-text-field
									solo
									hide-details
									color="#EBA900"
									append-icon="mdi-content-copy"
									:value="currencies.input_address"
								>
									<template v-slot:append>
										<v-btn
											icon
											color="#03A593"
											@click="$copy(currencies.input_address)"
										>
											<svg
												width="20"
												height="24"
												viewBox="0 0 20 24"
												fill="none"
												xmlns="http://www.w3.org/2000/svg"
											>
												<path
													d="M14.4 0.0232647C14.315 0.0232647 6.17142 0 6.17142 0C4.72044 0 3.42858 1.45576 3.42858 3.00002L2.63589 3.02026C1.1856 3.02026 0 4.45574 0 6V21C0 22.5443 1.29189 24 2.74287 24H13.0286C14.4795 24 15.7714 22.5443 15.7714 21H16.4571C17.9081 21 19.2 19.5443 19.2 18V6.01801L14.4 0.0232647ZM13.0286 22.5H2.74287C2.02287 22.5 1.37145 21.7643 1.37145 21V6C1.37145 4.88401 2.21076 4.50001 3.42858 4.50001V18C3.42858 19.5443 4.72044 21 6.17142 21C6.17142 21 13.6059 20.9918 14.4061 20.9918C14.4061 21.9023 13.8604 22.5 13.0286 22.5ZM14.4 15.7582H8.22858C7.85007 15.7582 7.54287 15.423 7.54287 15.009C7.54287 14.595 7.85007 14.2598 8.22858 14.2598H14.4C14.7785 14.2598 15.0857 14.595 15.0857 15.009C15.0857 15.423 14.7785 15.7582 14.4 15.7582ZM14.4 12.012H8.22858C7.85007 12.012 7.54287 11.6767 7.54287 11.2628C7.54287 10.8488 7.85007 10.5135 8.22858 10.5135H14.4C14.7785 10.5135 15.0857 10.8488 15.0857 11.2628C15.0857 11.676 14.7785 12.012 14.4 12.012ZM15.7714 6C15.0412 6 14.4 5.27927 14.4 4.50001C14.4 4.50001 14.4 2.98049 14.4 1.52326V1.52175L17.8286 6H15.7714Z"
													fill="#03A593"
												/>
											</svg>
										</v-btn> </template
								></v-text-field>
							</div>
						</div>
					</div>
					<div class="lg:t-col-span-1">
						<img
							:src="
								'https://chart.googleapis.com/chart?chs=100x100&chld=L|1&cht=qr&chl=' +
								currencies.input_address
							"
							alt
							class="white sm:t-w-full t-w-1/2 t-mx-auto"
						/>
					</div>
				</div>
			</v-card>
		</div>
		<v-card
			color="#19232A"
			class="
				t-border t-border-custom-03A593 t-max-w-full t-overflow-x-auto
				xl:t-col-span-6
			"
			id="history"
		>
			<!-- <v-card-actions
				class="t-px-4 t-border-t t-border-custom-EFEFEF py-3 t-border-opacity-20 t-bg-custom-1E4968"
			>
				<div class="t-grid 2xl:t-grid-cols-3 md:t-grid-cols-3 t-grid-cols-2 2xl:t-gap-8 t-gap-4">
					<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
						<label for="from" class="white--text">From</label>
						<v-menu
							ref="menu_to"
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
								<v-btn text color="primary" @click="$refs.menu_to.save(date_from)">OK</v-btn>
							</v-date-picker>
						</v-menu>
					</div>
					<div class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center">
						<label for="from" class="white--text">To</label>
						<v-menu
							ref="menu_form"
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
								<v-btn text color="primary" @click="$refs.menu_form.save(date_to)">OK</v-btn>
							</v-date-picker>
						</v-menu>
					</div>
					<div class="t-flex t-items-end t-pl-3">
						<v-btn color="#EBA900" class="t-w-full" @click="SearchDepsit()" elevation="0">Search</v-btn>
					</div>
				</div>
			</v-card-actions>-->
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
									{{ $t("walletPage.symbol") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("walletPage.txHash") }}
								</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(v, i) in histories_deposit.data"
								:key="i"
								class="
									hover:t-bg-custom-03A593 hover:t-bg-opacity-10
									t-transition-all t-duration-300
								"
								:class="[i % 2 ? 't-text-custom-03A593' : 't-text-white']"
							>
								<td class="t-text-center">{{ v.created_at }}</td>
								<td class="t-text-center">
									${{ v.amount | displayCurrency(2) }}
								</td>
								<td class="t-text-center">{{ v.symbol }}</td>
								<td class="t-text-center">{{ v.txhash }}</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
			</div>

			<v-card-actions
				class="
					t-px-4 t-border-t t-border-custom-EFEFEF
					pt-3
					t-border-opacity-20
				"
			>
				<div class="text-center t-w-full">
					<v-pagination
						v-model="page"
						:value="histories_deposit.current_page"
						:length="histories_deposit.last_page"
						@next="
							HistoriesDeposit({
								page: histories_deposit.current_page + 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@previous="
							HistoriesDeposit({
								page: histories_deposit.current_page - 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@input="(v) => HistoriesDeposit(v)"
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
	data: () => ({
		coin: null,
		coins: [
			{
				symbol: "btc",
				img: require("~/assets/images/coin/btc.svg"),
			},
			{
				symbol: "eth",
				img: require("~/assets/images/coin/eth.svg"),
			},
			{
				symbol: "ltc",
				img: require("~/assets/images/coin/ltc.svg"),
			},
			{
				symbol: "ripple",
				img: require("~/assets/images/coin/ripple.svg"),
			},
			{
				symbol: "usdt",
				img: require("~/assets/images/coin/usdt.svg"),
			},
		],
		page: 1,
		date_popup_from: false,
		date_popup_to: false,
		date_from: null,
		date_to: null,
	}),
	methods: {
		...mapActions("wallet", ["CURRENCIES", "HISTORIES_DEPOSIT"]),
		HistoriesDeposit() {
			const data = {
				page: this.page,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_DEPOSIT(data);
		},
		SearchDepsit() {
			const data = {
				page: 1,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_DEPOSIT(data);
		},
	},
	async mounted() {
		// if (this.$auth.user.admin_setup) {
		// 	this.$router.push("transfer");
		// 	this.$toasted.error("You are not permission in deposit.");
		// }
		await this.CURRENCIES();
		await this.HistoriesDeposit();
		this.coin = this.coins[4];
	},
	computed: {
		...mapState("wallet", ["histories_deposit", "currencies"]),
	},
};
</script>

<style></style>
