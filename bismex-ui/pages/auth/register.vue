<template>
	<v-form
		class="t-block"
		@submit.prevent="REGISTER(input.json())"
		:readonly="$load('register')"
	>
		<v-card-text class="t-mx-auto t-pb-0" v-if="!input.msg">
			<TabsRouter
				class="t-w-3/5 lg:t-w-2/5 t-mx-auto t-py-12"
				:router="router"
			/>
			<div class="t-grid lg:t-grid-cols-2 t-grid-cols-1 t-gap-5">
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('register.userName')"
					class="rounded-pill"
					v-model="input.username"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('register.email')"
					class="rounded-pill"
					type="email"
					v-model="input.email"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('register.password')"
					type="password"
					class="rounded-pill"
					v-model="input.password"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('register.confirmPassword')"
					type="password"
					class="rounded-pill"
					v-model="input.password_confirmation"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					:label="$t('register.phone')"
					class="rounded-pill"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					v-model="input.ref_id"
					solo
					:label="$t('register.refID')"
					class="rounded-pill"
				></v-text-field>
				<div class="lg:t-col-span-2">
					<v-checkbox
						class="t-mt-0 checkbox"
						off-icon="mdi-checkbox-blank-circle"
						on-icon="mdi-check-circle"
						v-model="input.accept"
					>
						<template #label>
							<span class="white--text">{{ $t("register.iAgree") }}</span>
						</template>
					</v-checkbox>
				</div>
			</div>
		</v-card-text>
		<v-card-text v-else class="t-mt-5 lg:t-w-2/3 t-mx-auto">
			<v-alert type="success">{{ input.msg.message }}</v-alert>
		</v-card-text>
		<v-card-actions class="t-justify-center t-items-center t-flex t-flex-col">
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				:disabled="!input.register"
				v-if="!input.msg"
				:loading="$load('register')"
				>{{ $t("register.signUp") }}</v-btn
			>
			<v-btn
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
				to="/login"
				v-else
			>
				<v-icon>mdi-arrow-left</v-icon>Sign in
			</v-btn>
		</v-card-actions>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";
import UserModel from "~/models/user.model";
export default {
	name: "login",
	layout: "auth",
	auth: false,
	data() {
		return {
			input: new UserModel(),
		};
	},
	computed: {
		router() {
			return [
				{
					name: this.$t("register.signIn"),
					path: "/login",
				},
				{
					name: this.$t("register.signUp"),
					path: "/register",
				},
			];
		},
	},
	methods: {
		async REGISTER(data) {
			this.input.accept = true;
			this.input.msg = await this.$store.dispatch("user/REGISTER", data, {
				root: true,
			});
		},
	},
	mounted() {
		if (this.$route.query.ref) this.input.ref_id = this.$route.query.ref;
	},
};
</script>
