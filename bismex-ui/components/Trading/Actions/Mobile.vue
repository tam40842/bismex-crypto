<template>
	<div class="t-grid t-grid-cols-5 t-auto-cols-max t-gap-1">
		<v-btn
			color="#CF304A"
			class="white--text t-col-span-2 t-text-xl"
			height="70"
			:disabled="!status || $load('SELL')"
			@click="sell_btn"
		>
			<div class="t-flex t-items-end" v-if="sell === 0">
				<v-img :src="$svg.sell" width="20"></v-img>
				<span class="t-pl-2 t-inline-block white--text t-font-black t-text-xl">{{
					$t("tradingPage.sell")
				}}</span>
			</div>
			<div class="t-block t-font-bold t-text-lg white--text" v-else>
				{{ Number(sell) }}
			</div>
		</v-btn>
		<v-sheet
			color="#4F4F5028"
			class="t-p-2 t-relative t-flex t-flex-col t-items-center t-w-full t-rounded t-text-center"
		>
			<v-img :src="$svg.timer" width="20" contain></v-img>
			<span
				class="t-font-bold notranslate"
				:class="[this.status ? 't-text-custom-03A593' : 't-text-custom-CF304A']"
				>{{ time }}s</span
			>
		</v-sheet>
		<v-btn
			color="#03A593"
			class="white--text t-col-span-2 t-text-xl"
			height="70"
			:disabled="!status || $load('BUY')"
			@click="buy_btn"
		>
			<div class="t-flex t-items-end" v-if="buy === 0">
				<v-img :src="$svg.buy" width="20"></v-img>
				<span class="t-pl-2 t-inline-block white--text t-font-black t-text-xl">{{
					$t("tradingPage.buy")
				}}</span>
			</div>
			<div class="t-block t-font-bold t-text-lg white--text" v-else>
				{{ Number(buy) }}
			</div>
		</v-btn>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	props: {
		status: {
			type: Boolean,
			default: true,
		},
		timer: {
			type: Number,
			default: 0,
		},
		buy: {
			type: Number,
			default: 0,
		},
		sell: {
			type: Number,
			default: 0,
		},
	},
	computed: {
		time() {
			if (this.timer > 30) {
				return 60 - this.timer;
			} else {
				return 30 - this.timer;
			}
		},
	},
	methods: {
		...mapActions("histories", ["HISTORIES_TYPE"]),
		buy_btn() {
			this.$emit("buy");
			// this.HISTORIES_TYPE("today");
		},
		sell_btn() {
			this.$emit("sell");
			// this.HISTORIES_TYPE("today");
		},
	},
};
</script>

<style></style>
