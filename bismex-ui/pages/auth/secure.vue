<template>
	<v-form class="t-block" @submit.prevent="LOGIN(input.json())" :readonly="$load('login')">
		<v-card-subtitle
			class="t-block t-text-center t-py-0 white--text t-font-medium"
		>Please enter security code</v-card-subtitle>
		<v-card-text class="t-mx-auto t-pb-0">
			<div class="t-grid t-grid-cols-1 t-gap-5">
				<v-text-field
					type="number"
					hide-details
					height="45"
					solo
					label="Two factor authentication"
					class="rounded-pill"
					v-model="input.twofa_code"
				></v-text-field>
			</div>
		</v-card-text>
		<v-card-actions class="t-justify-center t-items-center t-flex t-flex-col t-mt-5">
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				:disabled="!input.login"
				:loading="$load('login')"
			>Submit</v-btn>
			<v-btn text rounded color="white" class="t-mt-2" to="/login">
				<v-icon>mdi-keyboard-backspace</v-icon>
				<span>Back to sign in</span>?
			</v-btn>
		</v-card-actions>
	</v-form>
</template>

<script>
	import { mapActions, mapState } from "vuex";
	import UserModel from "~/models/user.model";
	export default {
		name: "login",
		auth: false,
		layout: "auth",
		data() {
			return {
				router: [
					{
						name: "Sign in",
						path: "/login",
					},
					{
						name: "Sign up",
						path: "/register",
					},
				],
				input: new UserModel(),
			};
		},
		computed: {
			...mapState("user", ["loading"]),
		},
		methods: {
			...mapActions("user", ["LOGIN"]),
		},
		beforeRouteEnter(to, from, next) {
			if (from.name === "login" && sessionStorage.getItem("2fa")) next();
			else next("/login");
		},
		mounted() {
			let json = JSON.parse(sessionStorage.getItem("2fa"));
			this.input.email = json.email;
			this.input.password = json.password;
		},
	};
</script>