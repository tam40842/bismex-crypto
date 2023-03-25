<template>
	<div class="t-flex t-flex-col t-h-full" ref="history">
		<v-sheet
			color="#9ca3af10"
			class="t-rounded t-overflow-hidden"
			v-if="height"
			:height="height"
		>
			<v-tabs
				color="#03A593"
				background-color="transparent"
				class="t-max-w-full history-tab"
			>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[(type = 'order'), Bot_Orders()]"
				>
					<small>{{ $t("tradingPage.orders") }}</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[(type = 'today'), Histories_Type('today')]"
				>
					<small>{{ $t("tradingPage.today") }}</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[(type = 'month'), Histories_Type('month')]"
				>
					<small>{{ $t("tradingPage.month") }}</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[(type = 'all'), Histories_Type('all')]"
				>
					<small>{{ $t("tradingPage.all") }}</small>
				</v-tab>
			</v-tabs>
			<div class="t-block t-px-2 t-flex-grow">
				<v-simple-table
					class="t-bg-transparent t-font-bold"
					id="history-full"
					fixed-header
					:height="height"
					v-if="type === 'order'"
				>
					<template v-slot:default>
						<thead>
							<tr class="t-bg-transparent">
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									{{ $t("tradingPage.id") }}
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									{{ $t("tradingPage.order") }}
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									{{ $t("tradingPage.invest") }}
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									{{ $t("tradingPage.time") }}
								</th>
							</tr>
						</thead>
						<tbody v-if="histories_orders.length > 0">
							<tr
								v-for="(v, i) in histories_orders"
								:key="i"
								:class="classStatus(v.action)"
							>
								<td class="t-w-1/4 t-text-center">#{{ v.orderid }}</td>
								<td class="t-w-1/4 t-text-center">{{ v.action }}</td>
								<td class="t-w-1/4 t-text-center">
									${{ v.amount | displayCurrency(2) }}
								</td>
								<td class="t-w-1/4 t-text-center">{{ v.created_at }}</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
				<v-simple-table
					class="t-bg-transparent t-font-bold"
					id="history-full"
					fixed-header
					:height="height - 110"
					v-else
				>
					<template v-slot:default>
						<thead>
							<tr class="t-bg-transparent">
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									Time
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									Order
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									Invest
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									Revence
								</th>
								<th
									class="t-bg-transparent text-center t-text-xs white--text t-w-1/4"
								>
									Finance
								</th>
							</tr>
						</thead>
						<tbody v-if="histories_orders.length > 0">
							<tr
								v-for="(v, i) in histories_orders"
								:key="i"
								:class="classHistory(v.status)"
							>
								<td class="t-w-1/4 t-text-xs">
									{{ v.created_at | displayDate("HH:mm:ss") }}
								</td>
								<!-- <td class="t-w-1/4 t-text-xs t-text-center">#{{ v.orderid }}</td> -->
								<td class="t-w-1/4 t-text-xs t-text-center">{{ v.action }}</td>
								<td class="t-w-1/4 t-text-xs t-text-center">
									${{ v.amount | displayCurrency(2) }}
								</td>
								<td class="t-text-xs t-text-center" v-if="v.status == 1">
									${{
										(v.amount + (v.amount * v.profit_percent) / 100)
											| displayCurrency(2)
									}}
								</td>
								<td class="t-text-xs t-text-center" v-else-if="v.status == 2">
									0
								</td>
								<td class="t-w-1/4 t-text-xs t-text-center" v-else>---</td>
								<td class="t-w-1/4 t-text-xs t-text-center">
									${{ v.total_balance | displayCurrency(2) }}
								</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
				<v-divider
					class="t-border-custom-EFEFEF t-border-opacity-50 t-py-1"
					v-if="type !== 'order'"
				/>
				<div
					class="t-grid t-grid-cols-2 t-gap-x-5 t-text-sm white--text"
					v-if="type !== 'order'"
				>
					<!-- <div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Begin:</span>
						<span>${{ begin | displayCurrency(2) }}</span>
					</div>-->
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Profit:</span>
						<span>{{ format_profit }}${{ profit | displayCurrency(2) }}</span>
					</div>
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Deposit:</span>
						<span>${{ histories_deposit | displayCurrency(2) }}</span>
					</div>
					<!-- <div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Waiting:</span>
						<span>${{ waiting | displayCurrency(2) }}</span>
					</div>-->
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Withdraw:</span>
						<span>${{ histories_withdraw | displayCurrency(2) }}</span>
					</div>
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Balance::</span>
						<span class="notranslate"
							>${{ $auth.user.live_balance | displayCurrency(2) }}</span
						>
					</div>
				</div>
			</div>
		</v-sheet>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	props: {
		border: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			type_tabs: true,
			action: true,
			height: null,
			table: 0,
			type: "today",
			mobile: false,
		};
	},
	sockets: {
		timer(e) {
			if (e.seconds === 0 && this.type !== "order")
				this.HISTORIES_TYPE(this.type);
			if (this.type_tabs === true && this.type === "order") {
				if (e.seconds < 30) {
					if (this.action == true) {
						this.action = false;
						this.BOT_ORDERS();
					}
					if (this.bot_orders[e.seconds]) {
						this.ORDER({ order: this.bot_orders[e.seconds] });
					}
				} else if (e.seconds > 30) {
					this.action = true;
				}
			}
		},
	},
	watch: {
		height() {
			// this.onResizeTable();
			return this.height;
		},
	},
	methods: {
		...mapActions("histories", ["HISTORIES_TYPE", "BOT_ORDERS", "ORDER"]),
		onResizeTable() {
			this.table = this.$refs.table.clientHeight;
		},
		onResize() {
			let getHeight = this.$refs.history.clientHeight;
			this.height = getHeight;
		},
		Histories_Type(type) {
			this.type_tabs = false;
			this.HISTORIES_TYPE(type);
		},
		Bot_Orders() {
			this.type_tabs = true;
			this.BOT_ORDERS();
		},
		classStatus(status) {
			let classType = "";
			switch (status) {
				case "BUY":
					classType = "t-text-[#03A593]";
					break;
				case "SELL":
					classType = "t-text-custom-CF304A";
					break;
				default:
					classType = "yellow--text darkden-2";
					break;
			}
			return classType;
		},
		classHistory(status) {
			let classType = "";
			switch (status) {
				case 1:
					classType = "t-text-custom-03A593";
					break;
				case 2:
					classType = "t-text-custom-CF304A";
					break;
				default:
					classType = "t-text-white darkden-2";
					break;
			}
			return classType;
		},
	},
	async mounted() {
		this.type = "order";
		this.onResize();
		window.addEventListener("resize", this.onResize());
	},
	computed: {
		...mapState("histories", [
			"histories_orders",
			"histories_deposit",
			"histories_withdraw",
			"waiting",
			"profit",
			"begin",
			"bot_orders",
			"format_profit",
		]),
	},
};
</script>

<style lang="scss">
.history-item {
	&:not(.v-tab--active) {
		color: white !important;
	}
	small {
		font-size: 9px !important;
	}
}
#history-full {
	th {
		font-size: 9px !important;
	}
	td {
		font-size: 9px !important;
		@apply t-py-1 t-h-auto;
	}
}
.history-tab {
	.v-tabs-bar {
		height: 40px;
	}
}
</style>
