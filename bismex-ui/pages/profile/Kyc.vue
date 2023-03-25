<template>
	<form
		class="t-grid xl:t-grid-cols-4 t-grid-cols-1 t-auto-rows-max t-gap-4 t-mx-auto"
		:disabled="$auth.user.kyc_status === 2"
		@submit.prevent="postKyc(input.json())"
	>
		<div
			class="xl:t-col-span-4 t-col-span-1"
			v-if="$auth.user.kyc_status !== 0"
		>
			<v-alert type="warning" v-if="$auth.user.kyc_status === 1">
				<b>{{ $t("profilePage.kycReviewing1") }}</b>
				<br />{{ $t("profilePage.kycReviewing2") }}
			</v-alert>

			<v-card
				alert
				dark
				type="success"
				v-else-if="$auth.user.kyc_status === 2"
				class="t-p-[80px] t-border-[#03A593] t-pb-[60px]"
			>
				<div class="t-flex t-items-center t-justify-center t-mb-16 t-pt-[60px]">
					<img
						class="object-cover object-center"
						src="~assets/images/kyc/checkmark.svg"
						alt=""
					/>
				</div>
				<span class="t-text-white t-text-lg t-font-light">{{
					$t("profilePage.kycSuccess")
				}}</span>
			</v-card>

			<v-alert type="error" v-else-if="$auth.user.kyc_status === 3">
				<b>{{ $t("profilePage.wedont") }}</b>
			</v-alert>
		</div>
		<div
			class="t-block xl:t-col-span-1 xl:t-row-span-2 t-order-2"
			v-if="$auth.user.kyc_status === 0 || $auth.user.kyc_status === 3"
		>
			<v-card color="#19232A" class="t-border t-border-[#0e635d] t-p-4">
				<div class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4">
					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.firstName") }}</span
						>

						<v-text-field
							background-color="#FFF"
							solo
							hide-details
							v-model="input.first_name"
						></v-text-field>
					</div>
					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.lastName") }}</span
						>

						<v-text-field
							background-color="#FFF"
							solo
							hide-details
							v-model="input.last_name"
						></v-text-field>
					</div>

					<!-- <v-menu
						ref="menu"
						v-model="date_popup"
						:close-on-content-click="false"
						:return-value.sync="date"
						transition="scale-transition"
						offset-y
						min-width="auto"
					>
						<template v-slot:activator="{ on }">
							<v-text-field
								background-color="#FFF"
								solo
								hide-details
								label="Birthday (*)"
								v-model="date"
								readonly
								v-on="on"
								class="t-cursor-pointer"
							>
								<template #append>
									<v-icon v-on="on">mdi-calendar</v-icon>
								</template>
							</v-text-field>
						</template>
						<v-date-picker v-model="date" no-title scrollable color="#EBA900">
							<v-spacer></v-spacer>
							<v-btn text color="primary" @click="date_popup = false">Cancel</v-btn>
							<v-btn text color="primary" @click="$refs.menu.save(date)">OK</v-btn>
						</v-date-picker>
					</v-menu> -->

					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.phone") }}</span
						>

						<v-text-field
							background-color="#FFF"
							v-model="input.phone_number"
							solo
							hide-details
							type="number"
						></v-text-field>
					</div>
					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.gender") }}</span
						>

						<v-text-field
							background-color="#FFF"
							solo
							hide-details
							v-model="input.gender"
						></v-text-field>
					</div>
					<!-- <v-text-field background-color="#FFF" solo hide-details label="Address (*)"></v-text-field> -->
					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.nationalId") }}</span
						>

						<v-text-field
							background-color="#FFF"
							solo
							hide-details
							v-model="input.passport"
							type="number"
						></v-text-field>
					</div>
					<div>
						<span
							class="t-text-white t-font-semibold t-text-base t-text-opacity-80"
							>{{ $t("profilePage.country") }}</span
						>

						<v-text-field
							background-color="#FFF"
							solo
							hide-details
							v-model="input.country"
						></v-text-field>
					</div>
					<v-btn
						color="#EBA900"
						class="white--text t-uppercase t-mt-6 mx-auto t-w-[60%]"
						style="background-color: #03A593"
						height="50"
						type="submit"
						:loading="$load('kyc')"
						>{{ $t("profilePage.submit") }}</v-btn
					>
				</div>
			</v-card>
		</div>
		<div
			class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 xl:t-col-span-3"
			v-if="$auth.user.kyc_status === 0 || $auth.user.kyc_status === 3"
		>
			<v-card color="#19232A" class="t-border t-border-[#0e635d] t-p-4">
				<ul
					class="t-list-disc white--text xl:t-text-base t-text-xs t-list-none"
				>
					<li>- {{ $t("profilePage.weWill") }}</li>
					<li>- {{ $t("profilePage.failureTo") }}</li>
					<li>- {{ $t("profilePage.uploadA") }}</li>
					<li>- {{ $t("profilePage.acceptable") }}</li>
				</ul>
			</v-card>
			<div
				class="t-grid xl:t-grid-flow-col t-grid-flow-row t-auto-cols-fr t-gap-4"
			>
				<v-card
					color="#19232A"
					class="xl:t-col-span-1 xl:t-row-span-1 t-border t-border-[#0e635d] t-px-4 t-pb-4"
				>
					<v-card-title
						class="text-center t-p-2 "
						style="word-break: normal !important"
					>
						<h3 class="t-mx-auto t-mb-0 t-text-3xl t-text-white t-font-bold">
							{{ $t("profilePage.backOf") }}
						</h3>
					</v-card-title>
					<input
						type="file"
						name="identity_backend"
						@change="(event) => upload({ e: event, l: 'identity_backend' })"
						id="identity_backend"
						hidden
					/>
					<label
						class="t-w-full t-h-40 t-pb-4 t-block t-relative t-cursor-pointer"
						for="identity_backend"
					>
						<img
							class="t-w-full t-h-full object-cover object-center"
							:src="
								input.identity_backend
									? $config.apiUrl + input.identity_backend
									: $kyc.front
							"
							alt
						/>
						<v-overlay absolute :value="$load('identity_backend')">
							<v-progress-circular
								color="#2BB99F"
								indeterminate
								size="64"
							></v-progress-circular>
						</v-overlay>
					</label>
					<ul class="t-list-disc t-text-xs grey--text darken-1 t-list-none">
						<li
							v-for="(v, i) in text.back"
							:key="i"
							class="t-text-sm t-text-white t-text-opacity-80"
						>
							- {{ v }}
						</li>
					</ul>
				</v-card>
				<v-card
					color="#19232A"
					class="xl:t-col-span-1 xl:t-row-span-1 t-border t-border-[#0e635d] t-px-4 t-pb-4"
				>
					<v-card-title
						class="text-center t-p-2 "
						style="word-break: normal !important"
					>
						<h3 class="t-mx-auto t-mb-0 t-text-3xl t-text-white t-font-bold">
							{{ $t("profilePage.frontOf") }}
						</h3>
					</v-card-title>
					<input
						type="file"
						name="identity_frontend"
						id="identity_frontend"
						hidden
						@change="(event) => upload({ e: event, l: 'identity_frontend' })"
					/>
					<label
						class="t-w-full t-h-40 t-pb-4 t-block t-relative t-cursor-pointer"
						for="identity_frontend"
					>
						<v-overlay absolute :value="$load('identity_frontend')">
							<v-progress-circular
								color="#2BB99F"
								indeterminate
								size="64"
							></v-progress-circular>
						</v-overlay>
						<img
							class="t-w-full t-h-full object-cover object-center"
							:src="
								input.identity_frontend
									? $config.apiUrl + input.identity_frontend
									: $kyc.front
							"
							alt
						/>
					</label>
					<ul class="t-list-disc t-text-xs grey--text darken-1 t-list-none">
						<li
							v-for="(v, i) in text.front"
							:key="i"
							class="t-text-sm t-text-white t-text-opacity-80"
						>
							- {{ v }}
						</li>
					</ul>
				</v-card>
				<v-card
					color="#19232A"
					class="xl:t-col-span-1 xl:t-row-span-1 t-border t-border-[#0e635d] t-px-4 t-pb-4"
				>
					<v-card-title
						class="text-center t-p-2 "
						style="word-break: normal !important"
					>
						<h3 class="t-mx-auto t-mb-0 t-text-3xl t-text-white t-font-bold">
							{{ $t("profilePage.yourSelfie") }}
						</h3>
					</v-card-title>
					<input
						type="file"
						name="selfie"
						id="selfie"
						hidden
						@change="(event) => upload({ e: event, l: 'selfie' })"
					/>
					<label
						class="t-w-full t-h-40 t-pb-4 t-block t-relative t-cursor-pointer"
						for="selfie"
					>
						<v-overlay absolute :value="$load('selfie')">
							<v-progress-circular
								color="#2BB99F"
								indeterminate
								size="64"
							></v-progress-circular>
						</v-overlay>
						<img
							class="t-w-full t-h-full object-cover object-center"
							:src="input.selfie ? $config.apiUrl + input.selfie : $kyc.selfie"
							alt
						/>
					</label>
					<ul class="t-list-disc t-text-xs grey--text darken-1 t-list-none">
						<li
							v-for="(v, i) in text.selfie"
							:key="i"
							class="t-text-sm t-text-white t-text-opacity-80"
						>
							- {{ v }}
						</li>
					</ul>
				</v-card>
			</div>
		</div>
	</form>
</template>

<script>
import { mapActions, mapState } from "vuex";
import UserModel from "~/models/user.model";
export default {
	data() {
		return {
			date_popup: false,
			date: null,
			text: {
				back: [
					"No modification",
					"Full image of the passport front cover",
					"Visible image",
				],
				front: [
					"No modification",
					"Full image of the passport front cover",
					"Visible image",
				],
				selfie: [
					"No modification",
					"The full face must be visible",
					"The passport page showing details must be readable.",
					'Note must include the word "Get To KNOW me" and the current date in handwriting',
					"The selfie must be clear & not blurry",
					"Your fingers aren’t covering any text",
				],
			},
			kyc: {
				first_name: "",
				last_name: "",
				gender: "",
				country: "",
				passport: "",
				identity_frontend: "",
				identity_backend: "",
				selfie: "",
			},
			input: new UserModel(),
		};
	},
	methods: {
		...mapActions("user", ["ON_CHANGE_IMAGE", "getKyc", "postKyc"]),
		async upload(data) {
			this.input.clear();
			await this.ON_CHANGE_IMAGE(data);
			this.getKycDocument();
		},
		async getKycDocument() {
			let res = await this.getKyc();
			this.input.first_name = res.data.kyc.first_name;
			this.input.last_name = res.data.kyc.last_name;
			this.input.country = res.data.kyc.country;
			this.input.phone_number = res.data.kyc.phone_number;
			this.input.passport = res.data.kyc.passport;
			this.input.gender = res.data.kyc.gender;
			this.input.selfie = res.data.kyc.selfie;
			this.input.identity_backend = res.data.kyc.identity_backend;
			this.input.identity_frontend = res.data.kyc.identity_frontend;
		},
	},
	async mounted() {
		await this.getKycDocument();
	},
};
</script>

<style></style>
