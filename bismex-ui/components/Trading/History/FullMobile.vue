<template>
	<div class="t-flex t-flex-col" ref="history">
		<v-sheet color="#9ca3af10" class="t-rounded" height="380">
			<v-tabs color="#FBAE45" background-color="transparent" class="t-max-w-full history-tab">
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[type = 'order', Bot_Orders()]"
				>
					<small>Orders</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[type = 'today', Histories_Type('today')]"
				>
					<small>Today</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[type = 'month', Histories_Type('month')]"
				>
					<small>Month</small>
				</v-tab>
				<v-tab
					class="history-item t-px-1 t-py-0 t-min-w-0 t-w-1/4 t-min-h-0 t-h-10"
					@click="[type = 'all', Histories_Type('all')]"
				>
					<small>All</small>
				</v-tab>
			</v-tabs>
			<div class="t-block t-px-2 t-flex-grow">
				<v-simple-table class="t-bg-transparent t-font-bold" id="history-full" fixed-header height="250">
					<template v-slot:default>
						<thead v-if="type === 'order'">
							<tr class="t-bg-transparent">
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">ID</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Order</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Invert</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Time</th>
							</tr>
						</thead>
						<thead v-else>
							<tr class="t-bg-transparent">
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Time</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Order</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Invest</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Revence</th>
								<th class="t-w-1/4 t-bg-transparent text-center t-text-xs white--text">Finance</th>
							</tr>
						</thead>
						<tbody v-if="histories_orders.length > 0 && type === 'order'">
							<tr v-for="(v, i) in histories_orders" :key="i" :class="classStatus(v.action)">
								<td class="t-w-1/4 t-text-center">#{{ v.orderid }}</td>
								<td class="t-w-1/4 t-text-center">{{ v.action }}</td>
								<td class="t-w-1/4 t-text-center">${{ v.amount | displayCurrency(2) }}</td>
								<td class="t-w-1/4 t-text-center">{{ v.created_at }}</td>
							</tr>
						</tbody>
						<tbody v-if="histories_orders.length > 0 && type !== 'order'">
							<tr v-for="(v, i) in histories_orders" :key="i" :class="classHistory(v.status)">
								<td class="t-w-1/4 t-text-xs t-h-6">{{ v.created_at | displayDate('HH:mm:ss') }}</td>
								<td class="t-w-1/4 t-text-xs t-h-6 t-text-center">{{ v.action }}</td>
								<td class="t-w-1/4 t-text-xs t-h-6 t-text-center">${{ v.amount | displayCurrency(2) }}</td>
								<td
									class="t-w-1/4 t-text-xs t-h-6 t-text-center"
									v-if="v.status == 1"
								>${{ (v.amount * v.profit_percent / 100) | displayCurrency(2) }}</td>
								<td
									class="t-text-xs t-h-6 t-text-center"
									v-else-if="v.status == 2"
								>0</td>
								<td class="t-text-xs t-h-6 t-text-center" v-else>---</td>
								<td class="t-text-xs t-h-6 t-text-center">${{ v.total_balance | displayCurrency(2) }}</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
				<v-divider class="t-border-custom-EFEFEF t-border-opacity-50 t-py-1" v-if="type !== 'order'" />
				<div class="t-grid t-grid-cols-2 t-gap-x-5 t-text-xs white--text" v-if="type !== 'order'">
					<!-- <div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Begin:</span>
						<span>${{ begin | displayCurrency(2) }}</span>
					</div> -->
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
						<span>${{ waiting | displayCurrency}}</span>
					</div> -->
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Withdraw:</span>
						<span>${{ histories_withdraw | displayCurrency(2) }}</span>
					</div>
					<div class="t-flex t-justify-between t-py-1">
						<span class="t-text-custom-49D3FF">Balance::</span>
						<span class="notranslate">${{ $auth.user.live_balance | displayCurrency(2) }}</span>
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
				table: 0,
				type: "order",
				action: true,
				type_tabs: true,
			};
		},
		sockets: {
			timer(e) {
				if (e.seconds === 0 && this.type !== 'order') this.HISTORIES_TYPE(this.type);
				if (this.type_tabs === true && this.type ==='order') {
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
		methods: {
			...mapActions("histories", ["HISTORIES_TYPE", "BOT_ORDERS", "ORDER"]),
			onResizeTable() {
				this.table = this.$refs.table.clientHeight;
			},
			Histories_Type(type) {
				this.HISTORIES_TYPE(type);
			},
			classStatus(status) {
				let classType = "";
				switch (status) {
					case "BUY":
						classType = "t-text-custom-03A593";
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
		// async mounted() {
		// 	await this.HISTORIES_TYPE(this.type);
		// },
		computed: {
			...mapState("histories", [
				"histories_orders",
				"histories_deposit",
				"histories_withdraw",
				"waiting",
				"profit",
				"begin",
				"bot_orders",
				"format_profit"
			]),
		},
		mounted() {
			this.type = "order";
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
		height: 30px;
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
