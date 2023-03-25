<template>
	<v-form
		class="t-block"
		@submit.prevent="login()"
		:readonly="$load('login')"
	>
		<v-card-subtitle
			class="t-block t-text-center t-py-0 white--text t-font-medium"
		>Enter new password and password confirmation</v-card-subtitle>
		<v-card-text class="t-mx-auto t-py-5">
			<div class="t-grid t-grid-cols-1 t-gap-5">
				<v-text-field
					hide-details
					height="45"
					solo
					label="Password"
					type="password"
					class="rounded-pill"
					v-model="password"
				></v-text-field>
				<v-text-field
					hide-details
					height="45"
					solo
					label="Password Confirmation"
					type="password"
					class="rounded-pill"
					v-model="password_confirmation"
				></v-text-field>
			</div>
		</v-card-text>
		<v-card-actions class="t-justify-center t-items-center t-flex t-flex-col">
			<v-btn
				type="submit"
				rounded
				height="45"
				width="150"
				color="btn-primary white--text"
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
	import { mapActions } from "vuex";
	export default {
		name: "reset",
		layout: "auth",
		auth: false,
		data() {
			return {
        password: "",
			  password_confirmation: "",
			};
		},
		methods: {
			...mapActions("user", ["postReset"]),
			login() {
        this.postReset({
          token: this.$route.query.token,
          password: this.password,
          password_confirmation: this.password_confirmation,
        })
          .then((data) => {
            if (data.status == 422) {
              this.$toasted.error(data.message);
            } else {
              this.$toasted.success(data.message);
              window.location = "/login";
            }
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.errors = error.response.data.errors;
            }
          });
      },
		},
		beforeRouteEnter(to, from, next) {
			if (to.query.token) next();
			else next("/login");
		},
		// mounted() {
		// 	if (this.$route.query.token) this.input.token = this.$route.query.token;
		// },
	};
</script>
