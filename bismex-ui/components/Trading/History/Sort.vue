<template>
	<v-sheet color="#9ca3af10" class="t-rounded t-max-w-full t-overflow-hidden">
		<div class="t-flex">
      <div class="t-flex" style="width:35px;">
        <v-btn icon color="transparent" height="90px" class="t-mt-1" @click="openHistory($event)">
						<v-img :src="$svg.view_history" contain max-height="90"></v-img>
				</v-btn>
      </div>
			<div class="t-flex-grow">
				<v-tabs
					color="#03A593"
					background-color="transparent"
					class="t-max-w-full t-text-xs"
					height="30"
				>
					<v-tab
						class="history-item t-px-1 t-min-w-0 t-w-1/4"
						@click="[(type = 'order'), Bot_Orders()]"
						>{{ $t("tradingPage.orders") }}</v-tab
					>
					<v-tab
						class="history-item t-px-1 t-min-w-0 t-w-1/4"
						@click="[(type = 'today'), Histories_Type('today')]"
						>{{ $t("tradingPage.today") }}</v-tab
					>
					<v-tab
						class="history-item t-px-1 t-min-w-0 t-w-1/4"
						@click="[(type = 'month'), Histories_Type('month')]"
						>{{ $t("tradingPage.month") }}</v-tab
					>
					<v-tab
						class="history-item t-px-1 t-min-w-0 t-w-1/4"
						@click="[(type = 'all'), Histories_Type('all')]"
						>{{ $t("tradingPage.all") }}</v-tab
					>
				</v-tabs>
				<div class="t-w-full t-overflow-x-auto">
					<v-simple-table
						class="t-bg-transparent t-font-bold"
						fixed-header
						height="250"
						id="history-sort"
					>
						<template v-slot:default>
							<tbody v-if="histories_orders.length > 0 && type === 'order'">
								<tr
									v-for="(v, i) in histories_orders"
									:key="i"
									:class="classStatus(v.action)"
								>
									<td class="t-w-1/4 t-text-center t-text-xs t-h-5">
										#{{ v.orderid }}
									</td>
									<td class="t-w-1/4 t-text-center t-text-xs t-h-5">
										{{ v.action }}
									</td>
									<td class="t-w-1/4 t-text-center t-text-xs t-h-5">
										${{ v.amount | displayCurrency(2) }}
									</td>
									<td class="t-w-1/4 t-text-center t-text-xs t-h-5">
										{{ v.created_at }}
									</td>
								</tr>
							</tbody>
							<tbody v-if="histories_orders.length > 0 && type !== 'order'">
								<tr
									v-for="(v, i) in histories_orders"
									:key="i"
									:class="classHistory(v.status)"
								>
									<td class="t-w-1/4 t-text-xs t-h-5 t-text-center">
										{{ v.created_at | displayDate("HH:mm:ss") }}
									</td>
									<td class="t-w-1/4 t-text-xs t-h-5 t-text-center">
										{{ v.action }}
									</td>
									<td class="t-w-1/4 t-text-xs t-h-5 t-text-center">
										${{ v.amount | displayCurrency(2) }}
									</td>
									<td
										class="t-w-1/4 t-text-xs t-h-5 t-text-center"
										v-if="v.status == 1"
									>
										${{
											((v.amount * v.profit_percent) / 100) | displayCurrency(2)
										}}
									</td>
									<td
										class="t-text-xs t-h-5 t-text-center"
										v-else-if="v.status == 2"
									>
										0
									</td>
									<td class="t-text-xs t-h-5 t-text-center" v-else>---</td>
									<td class="t-text-xs t-h-5 t-text-center">
										${{ v.total_balance | displayCurrency(2) }}
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
					<div v-if="histories_orders.length > 0 && type !== 'order'">
						<table class="table t-w-full t-font-bold">
							<tbody class="t-w-full t-text-white t-mb-3">
								<tr>
									<!-- <td class="t-text-custom-49D3FF">Begin:</td>
									<td class="t-text-right t-pr-2">${{ begin | displayCurrency(2) }}</td> -->
									<td class="t-text-custom-49D3FF">{{$t('tradingPage.profit')}}:</td>
									<td class="t-text-right t-pr-2">
										{{ format_profit }}${{ profit | displayCurrency(2) }}
									</td>
									<td class="t-text-custom-49D3FF">Balance:</td>
									<td class="t-text-right t-pr-2 notranslate">
										${{ $auth.user.live_balance | displayCurrency(2) }}
									</td>
								</tr>
								<tr>
									<td class="t-text-custom-49D3FF">Deposit:</td>
									<td class="t-text-right t-pr-2">
										${{ histories_deposit | displayCurrency(2) }}
									</td>
									<td class="t-text-custom-49D3FF">Withdraw:</td>
									<td class="t-text-right t-pr-2">
										${{ histories_withdraw | displayCurrency(2) }}
									</td>
									<!-- <td class="t-text-custom-49D3FF">Waiting:</td>
									<td class="t-text-right t-pr-2">${{ waiting | displayCurrency(2) }}</td> -->
								</tr>
							</tbody>
						</table>
						<div
							:style="
								!$store.state.settings.orientation
									? 'height:230px;'
									: 'height:130px;'
							"
						></div>
					</div>
				</div>
			</div>
		</div>
	</v-sheet>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			type: "order",
			action: true,
			type_tabs: true,
		};
	},
	sockets: {
		timer(e) {
			if (e.seconds == 0 && this.type !== "order")
				this.HISTORIES_TYPE(this.type);
			if (this.type_tabs == true && this.type === "order") {
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
		Histories_Type(type) {
			this.HISTORIES_TYPE(type);
		},
		open(v) {
			this.$emit("open", v);
		},
		Bot_Orders() {
			this.BOT_ORDERS();
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
    openHistory(v){
      this.$emit("openHistory", v);
    }
	},
	computed: {
		...mapState("histories", [
			"histories_orders",
			"bot_orders",
			"waiting",
			"profit",
			"begin",
			"histories_withdraw",
			"histories_deposit",
			"format_profit",
		]),
	},
	mounted() {
		this.type = "order";
	},
};
</script>

<style lang="scss">
.v-tab.history-item {
	font-size: 9px;
	@screen md {
		@apply t-text-base;
	}
	&:not(.v-tab--active) {
		color: white !important;
	}
}
#history-sort {
	th,
	td {
		font-size: 9px !important;
		@screen md {
			@apply t-text-base;
		}
	}
}
</style>
