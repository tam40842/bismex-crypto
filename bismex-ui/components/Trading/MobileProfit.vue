<template>
	<div
		class="t-flex t-items-center t-bg-custom-4F4F5028 t-p-1 t-rounded t-text-custom-03A593 t-font-bold"
	>
		<v-img :src="$svg.money_bag" max-width="40" contain></v-img>
		<div class="t-flex-grow t-text-center t-text-xl">+${{ value + profit | displayCurrency(2)}}</div>
		<span>{{ fee }}%</span>
	</div>
</template>

<script>
	export default {
		props: {
			value: {
				type: Number,
				default: 0,
			},
		},
		data() {
			return {
				fee: 0,
			};
		},
		computed: {
			profit() {
				return (this.value * this.fee) / 100;
			},
		},
		sockets: {
			timer(e) {
				this.fee = e.fee;
			},
		},
	};
</script>

<style>
</style>
