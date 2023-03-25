<template>
	<v-form
		class="t-block"
		@submit.prevent="LOGIN(input.json())"
		:readonly="$load('login')"
	>
		<v-card-text class="t-mx-auto t-pb-0">
			<TabsRouter
				class="t-w-3/5 lg:t-w-2/5 t-mx-auto t-py-12"
				:router="router"
			/>
			<div class="t-grid t-grid-cols-1 t-gap-5">
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('login.email')"
					class="rounded-pill"
					v-model="input.email"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('login.password')"
					:type="input.password_show ? 'password' : 'text'"
					class="rounded-pill"
					v-model="input.password"
				>

					<template v-slot:append>
            <v-btn icon v-if="input.password_show" color="#03A593" @click="input.password_show = !input.password_show">
						  <v-icon>mdi-eye</v-icon>
						</v-btn>
						<v-btn icon v-else color="#03A593" @click="input.password_show = !input.password_show">
						  <v-icon>mdi-eye-off</v-icon>
						</v-btn>
					</template>
				</v-text-field>
			</div>
		</v-card-text>
		<v-card-actions
			class="t-justify-center t-items-center t-flex t-flex-col t-mt-5 t-gap-5"
		>
			<v-btn
				text
				rounded
				color="#03A593"
				class="t-mt-2 t-flex t-self-end "
				to="/forgot"
				>{{ $t("login.forgotPass") }}</v-btn
			>
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				:loading="$load('login')"
				:disabled="!input.login"
				>{{ $t("login.loginNow") }}</v-btn
			>
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
			input: new UserModel(),
		};
	},
	computed: {
		router() {
			return [
				{
					name: this.$t("login.signIn"),
					path: "/login",
				},
				{
					name: this.$t("login.signUp"),
					path: "/register",
				},
			];
		},
	},
	methods: {
		...mapActions("user", ["LOGIN"]),
	},
};
</script>
