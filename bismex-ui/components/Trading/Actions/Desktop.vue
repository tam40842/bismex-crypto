<template>
	<div class="t-flex t-flex-col">
		<v-sheet
			color="#4F4F5028"
			class="t-p-3 t-relative t-w-full t-rounded t-text-center t-mb-4"
		>
			<!-- <span
				class="t-text-2xl t-font-bold notranslate"
				:class="[this.status ? 't-text-custom-03A593' : 't-text-custom-CF304A']"
				>{{ time }}s</span
			> -->
			<v-progress-circular
				:rotate="90"
				:size="100"
				:width="15"
				:value="value"
				:class="[status ? 't-text-[#03A593]' : 't-text-[#CF304A]']"
			>
				<template v-slot:default>
					<div class="t-flex t-flex-col t-gap-2">
						<div>{{ time }}s</div>
					</div>
				</template>
			</v-progress-circular>
		</v-sheet>
		<div class="t-flex-grow t-grid t-grid-rows-2 t-gap-4">
			<v-btn
				color="#03A593"
				class="white--text t-text-xl"
				style="clip-path: polygon(50% 0, 100% 40%, 100% 100%, 0 100%, 0 40%); height: 75px;"
				x-large
				:disabled="!status || $load('BUY')"
				@click="buy_btn"
			>
				<div class="t-flex t-items-end" v-if="buy === 0">
					<v-img :src="$svg.buy"></v-img>
					<span class="t-p1-2 t-inline-block white--text">{{
						$t("tradingPage.buy")
					}}</span>
				</div>
				<div class="t-block t-font-bold t-text-lg white--text" v-else>
					{{ buy }}
				</div>
			</v-btn>
			<v-btn
				color="#CF304A"
				class="white--text t-text-xl"
				style="clip-path: polygon(0 0, 100% 0, 100% 60%, 50% 100%, 0 60%); height: 75px;"
				x-large
				:disabled="!status || $load('SELL')"
				@click="sell_btn"
			>
				<div class="t-flex t-items-end" v-if="sell === 0">
					<v-img :src="$svg.sell"></v-img>
					<span class="t-p1-2 t-inline-block white--text">{{
						$t("tradingPage.sell")
					}}</span>
				</div>
				<div class="t-block t-font-bold t-text-lg white--text" v-else>
					{{ sell }}
				</div>
			</v-btn>
		</div>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			interval: {},
			value: 100,
		};
	},
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
	mounted() {
		// this.interval = setInterval(() => {
		// 	if (this.value === 0 && this.time === 0) {
		// 		this.value = 100;
		// 		this.time = 30;
		// 		return this.value, this.time;
		// 	}
		// 	this.value -= 10 / 3;
		// 	this.time -= 1;
		// }, 1000);
		this.interval = setInterval(() => {
			if (this.value === 0) {
				this.value = 100;
				return this.value;
			}
			this.value -= 10 / 3;
		}, 1000);
	},
};
</script>

<style></style>
