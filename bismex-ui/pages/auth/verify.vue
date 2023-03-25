<template>
	<v-form class="t-block" @submit.prevent="LOGIN(input.json())" :readonly="$load('login')">
		<v-card-subtitle
			class="t-block t-text-center t-py-0 white--text t-font-medium"
		>Verify Account</v-card-subtitle>
		<v-card-text v-if="verified === true" class="t-mt-5 t-mx-auto" color="#2BB99F">
			<v-alert type="success">{{ message }}</v-alert>
		</v-card-text>
		<v-card-text v-else class="t-mt-5 t-mx-auto" color="#2BB99F">
			<v-alert type="error">{{ message }}</v-alert>
		</v-card-text>
		<v-card-text class="t-mx-auto t-pb-0">
			<TabsRouter class="lg:t-w-2/5 t-mx-auto t-py-12" :router="router" />
		</v-card-text>
		<!-- <v-card-actions class="t-justify-center t-items-center t-flex t-flex-col t-mt-5">
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				:loading="$load('login')"
				:disabled="!input.login"
			>Login Now</v-btn>
			<v-btn text rounded color="white" class="t-mt-2" to="/forgot">Forgot password?</v-btn>
		</v-card-actions> -->
	</v-form>
</template>

<script>
	import { mapActions, mapState } from "vuex";
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
                token: this.$route.query.token || ""
			};
		},
        computed: {
            ...mapState("user", ["verified", "message"])
        },
        mounted() {
            if (this.token != "") {
            	this.getVerify(this.token);
            }
        },
		methods: {
            ...mapActions("user", ["getVerify"]),
		},
	};
</script>