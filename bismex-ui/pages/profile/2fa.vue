<template>
	<v-card color="#19232A" class="t-border t-border-[#0e635d] t-p-4">
		<form
			@submit.prevent="postTwoFA"
			class="lg:t-col-span-1 t-gap-4 t-grid-flow-row t-grid t-auto-rows-max t-py-[32px] t-px-[74px]"
		>
			<p
				class="white--text t-text-base"
				v-if="$auth.user.google2fa_enable === 0"
			>
				{{ $t("profilePage.downloadGoogleStart") }}
				<a href class="t-text-[#03A593]">{{ $t("profilePage.chPlay") }}</a>
				{{ $t("profilePage.or") }}
				<a href class="t-text-[#03A593]"> {{ $t("profilePage.appStore") }}</a>
				{{ $t("profilePage.downloadGoogleEnd") }}
			</p>
			<div class="t-block t-text-center">
				<v-sheet color="white" class="t-inline-block t-rounded-full t-px-0">
					<v-img
						:src="
							'https://chart.googleapis.com/chart?chs=200x200&chld=L|1&cht=qr&chl=otpauth://totp/(Bitsmex)' +
								$auth.user.username +
								'?secret=' +
								$auth.user.google2fa_secret
						"
						width="200"
						v-if="$auth.user.google2fa_enable === 0"
					></v-img>
					<v-avatar v-else size="200" rounded>
						<v-icon size="200" color="#03A593">mdi-check-circle</v-icon>
					</v-avatar>
				</v-sheet>
			</div>
			<div class="t-block" v-if="$auth.user.google2fa_enable === 0">
				<div class="t-text-center t-text-[#03A593] t-text-lg t-font-bold">
					{{ $t("profilePage.secretKey") }}
				</div>
				<!-- <v-text-field
					:value="$auth.user.google2fa_secret"
					color="#EBA900"
					solo
					append-icon="mdi-map-marker"
					outlined
					hide-details
					class="sm:lg:t-w-1/3 sm:t-w-1/2 t-t-w-full t-mx-auto"
					@click:append="$copy($auth.user.google2fa_secret)"
				></v-text-field> -->
				<v-text-field
					:value="$auth.user.google2fa_secret"
					color="white"
					solo
					append-icon="mdi-content-copy"
					class="sm:lg:t-w-1/3 sm:t-w-1/2 t-t-w-full t-mx-auto twofa"
					style="color: white !important"
					background-color="transparent"
				></v-text-field>
			</div>
			<div
				class="t-block t-text-[#FFE600] t-font-bold t-text-center"
				v-if="$auth.user.google2fa_enable === 0"
			>
				{{ $t("profilePage.warningCode") }}
			</div>
			<div class="t-block">
				<div class="t-text-center t-text-[#03A593] t-font-bold t-text-lg">
					{{ $t("profilePage.twoFactor") }}
				</div>
				<v-text-field
					background-color="#FFF"
					solo
					v-model="twofa.twofa_code"
					hide-details
					type="number"
					class="input-center sm:lg:t-w-1/3 sm:t-w-1/2 t-t-w-full t-mx-auto  "
					:label="$t('profilePage.label2FA')"
				></v-text-field>
			</div>
			<div class="t-text-center">
				<v-btn
					color="#EBA900"
					type="submit"
					:loading="$load('two_fa')"
					class="white--text t-uppercase t-bg-[#03A593] t-text-lg"
					style="padding:0 24px"
				>
					{{ $t("profilePage.submit") }}
				</v-btn>
			</div>
		</form>
	</v-card>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data: () => ({
		twofa: {
			twofa_code: null,
		},
	}),
	methods: {
		...mapActions("user", ["TWO_FA"]),
		async postTwoFA() {
			const data = {
				twofa_code: this.twofa.twofa_code,
			};
			await this.TWO_FA({
				data,
				callback: async () => {
					this.twofa.twofa_code = "";
					this.$auth.fetchUser();
				},
			});
		},
	},
};
</script>

<style>
.v-input__slot {
	border: 4px solid #0e635d !important;
}

.mdi-content-copy::before {
	color: #0e635d;
}
</style>
