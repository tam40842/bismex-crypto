<template>
	<v-form class="t-block" @submit.prevent="postForgot(input.json())" :readonly="$load('login')">
		<v-card-subtitle
			class="t-block t-text-center t-py-0 white--text t-font-medium"
		>Please enter your email and recovery password</v-card-subtitle>
		<v-card-text class="t-mx-auto t-pb-0 t-mt-5" v-if="!input.msg">
			<div class="t-grid t-grid-cols-1 t-gap-5">
				<v-text-field
					hide-details
					height="45"
					solo
					label="Email Address"
					type="email"
					class="rounded-pill"
					v-model="input.email"
				></v-text-field>
			</div>
		</v-card-text>
		<v-card-text v-else class="t-mt-5 t-mx-auto" color="#2BB99F">
			<v-alert type="success">{{ input.msg.message }}</v-alert>
		</v-card-text>
		<v-card-actions class="t-justify-center t-items-center t-flex t-flex-col t-mt-5">
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				:loading="$load('login')"
				v-if="!input.msg"
			>Submit</v-btn>
			<v-btn text color="white" rounded class="t-grid t-grid-flow-col t-gap-3 t-mt-5" to="/login">
				<v-icon>mdi-keyboard-backspace</v-icon>
				<span>Back to sign in</span>
			</v-btn>
		</v-card-actions>
	</v-form>
</template>

<script>
	import { mapActions } from "vuex";
	import UserModel from "~/models/user.model";
	export default {
		layout: "auth",
		auth: false,
		data() {
			return {
				input: new UserModel(),
			};
		},
		methods: {
			async postForgot(data) {
				this.input.msg = await this.$store.dispatch(
					"user/postForgot",
					data,
					{
						root: true,
					}
				);
			},
		},
	};
</script>

<style>
</style>