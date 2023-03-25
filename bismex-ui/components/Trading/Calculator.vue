<template>
	<div class="t-grid t-grid-flow-row t-auto-rows-max">
		<div class="t-flex t-items-center t-justify-between t-my-1">
			<v-btn
				class="t-px-1 t-mx-1 white--text t-font-bold"
				x-small
				height="40"
				color="#4F4F5028"
				@click="change(Number(value) / 2)"
			>÷2</v-btn>
			<v-btn
				class="t-px-1 t-mx-1 white--text t-font-bold"
				x-small
				height="40"
				color="#4F4F5028"
				@click="change(Number(value) - 1)"
				>-</v-btn
			>
			<v-text-field
				color="#FFFFFF"
				background-color="transparent"
				class="text-center white--text calculator t-text-lg calculator"
				solo
				hide-details
				full-width
				height="100%"
				type="number"
				v-model="input"
				@change="change"
			></v-text-field>
			<v-btn
				class="t-px-1 t-mx-1 white--text t-font-bold"
				x-small
				height="40"
				color="#4F4F5028"
				@click="change(Number(value) + 1)"
				>+</v-btn
			>
			<v-btn
				@click="change(Number(value) * 2)"
				class="t-px-1 t-mx-1 white--text t-font-bold t-lowercase"
				x-small
				height="40"
				color="#4F4F5028"
			>
				<small>x</small> 2
			</v-btn>
			<!-- <div class="row">
				<div class="col-3">
					<button v-on:click="count++" class="btn btn-success">
						<i class="fa fa-plus-circle"></i>
					</button>
				</div>
				<div class="col-6">
					<input v-model.number="count" class="form-control" />
				</div>
				<div class="col-3">
					<button
						v-on:click="count--"
						v-bind:disabled="count < 1"
						class="btn btn-danger"
					>
						<i class="fa fa-minus-circle"></i>
					</button>
				</div>
			</div> -->
		</div>
		<div
			class="md:t-grid t-grid t-grid-flow-col t-auto-cols-fr t-gap-3 t-mb-1"
		>
			<div class="t-text-center" v-for="item in list" :key="item">
				<v-btn
					fab
					x-small
					outlined
					color="white"
					class="btn-calculator"
					@click="change(Number(value) + item)"
					>{{ item }}</v-btn
				>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		value: {
			type: Number,
			default: 0,
		},
		list: {
			type: Array,
			default: () => [5, 10, 20, 50, 100],
		},
	},
	computed: {
		input: {
			get() {
				return this.value;
			},
			set(v) {
				this.change(v);
			},
		},
	},
	methods: {
		change(v) {
			let count = 0;
			if (v >= 0) count = v;
			else count = 0;
			this.$emit("change", count);
		},
	},
};
</script>

<style>
.white--text input {
	color: white !important;
}
.calculator .v-input__slot {
	min-height: 40px !important;
}
</style>
