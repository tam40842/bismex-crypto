<template>
	<div class="container">
		<div class="t-grid lg:t-grid-cols-5 sm:t-grid-cols-3 t-grid-cols-1 t-gap-4">
			<v-card class="t-col-span-1 t-relative" color="#3FD0FF">
				<v-card-text class="t-flex t-justify-center">
					<v-avatar size="90" color="white" class="t-mx-auto t-cursor-pointer">
						<v-img
							:src="
								$auth.user.avatar
									? $config.apiUrl + $auth.user.avatar
									: $image.avatar_default
							"
							contain
							height="90"
							@click="$refs.avatar.click()"
						></v-img>
					</v-avatar>
					<input
						type="file"
						name="avatar"
						id="avatar"
						ref="avatar"
						hidden
						@change="(event) => postUploadAvatar({ e: event, l: 'avatar' })"
					/>
				</v-card-text>
				<v-overlay absolute :value="$load('avatar')">
					<v-progress-circular
						color="#EBA900"
						indeterminate
						size="64"
					></v-progress-circular>
				</v-overlay>
			</v-card>
			<v-card
				color="#19232A"
				elevation="0"
				class="t-border-[#0e635d] sm:t-col-span-2 t-col-span-1"
			>
				<v-card-title
					class="t-font-bold t-block t-text-custom-EFEFEF t-text-center"
					>{{ $t("profilePage.information") }}</v-card-title
				>
				<v-card-text class="t-text-custom-EFEFEF t-px-4">
					<div class="t-block t-text-center md:t-text-left">
						Your user: {{ $auth.user.username }}
					</div>
					<div class="t-block t-text-center md:t-text-left">
						Your email: {{ $auth.user.email }}
					</div>
				</v-card-text>
			</v-card>
			<v-card
				color="#19232A"
				elevation="0"
				class="t-border-[#0e635d] sm:t-col-span-2 t-col-span-1"
			>
				<v-card-title
					class="t-font-bold t-block t-text-custom-EFEFEF t-text-center"
					>{{ $t("profilePage.security") }}</v-card-title
				>
				<v-card-text class="t-text-custom-EFEFEF t-px-8">
					<div class="t-block t-text-lg t-mb-2 t-text-center md:t-text-left">
						{{ $t("profilePage.2fa") }}:
						<span
							class="t-text-[#03A593] accent-3"
							v-if="$auth.user.google2fa_enable == 1"
							>{{ $t("profilePage.complete") }}</span
						>
						<span class="t-text-[#03A593] accent-3" v-else>{{
							$t("profilePage.nonVerified")
						}}</span>
					</div>
					<div class="t-block t-text-lg t-text-center md:t-text-left">
						{{ $t("profilePage.kyc") }}:
						<span
							class="t-text-[#03A593] accent-3"
							v-if="$auth.user.kyc_status == 2"
							>{{ $t("profilePage.verified") }}</span
						>
						<span class="t-text-[#03A593] accent-3" v-else>{{
							$t("profilePage.nonVerified")
						}}</span>
					</div>
				</v-card-text>
			</v-card>
		</div>
		<v-card color="#19232A" class="2 t-border t-border-[#0e635d] t-p-4">
			<v-card-title class="t-pt-0 t-mb-16">
				<h3 class="mx-auto t-capitalize t-text-custom-EFEFEF t-font-bold">
					{{ $t("profilePage.resetPassword") }}
				</h3>
			</v-card-title>
			<form
				@submit.prevent="postChangePassword"
				class="t-grid sm:t-grid-rows-2 t-gap-4"
			>
				<div class="t-grid sm:t-grid-cols-3 t-grid-cols-1 t-gap-4 t-mb-8">
					<div>
						<span class="t-text-white t-font-semibold t-text-base">{{
							$t("profilePage.currentPassword")
						}}</span>
						<v-text-field
							background-color="#FFF"
							solo
							type="password"
							outlined
							v-model="passChange.current_password"
						></v-text-field>
					</div>
					<div>
						<span class="t-text-white t-font-semibold t-text-base">{{
							$t("profilePage.newPassword")
						}}</span>
						<v-text-field
							background-color="#FFF"
							solo
							type="password"
							outlined
							v-model="passChange.password"
							hide-details
						></v-text-field>
					</div>
					<div>
						<span class="t-text-white t-font-semibold t-text-base">{{
							$t("profilePage.confirmPassword")
						}}</span>
						<v-text-field
							background-color="#FFF"
							v-model="passChange.password_confirmation"
							solo
							outlined
							type="password"
							hide-details
						></v-text-field>
					</div>
				</div>
				<div class="t-block t-text-center">
					<v-btn
						type="submit"
						color="#EBA900"
						class="white--text t-uppercase t-mx-auto t-text-xl"
						height="48"
						width="200"
						style="background-color: #03A593"
						>{{ $t("profilePage.submit") }}</v-btn
					>
				</div>
			</form>
		</v-card>
		<v-card color="#19232A" class="t-border t-border-[#0e635d]">
			<v-card-title class="t-p-4">
				<h3 class="mx-auto t-text-custom-EFEFEF t-font-bold t-uppercase">
					{{ $t("profilePage.tokenKey") }}
				</h3>
			</v-card-title>
			<v-simple-table class="t-bg-transparent" fixed-header height="300">
				<template v-slot:default>
					<thead>
						<tr class="t-bg-transparent white--text">
							<th class="t-bg-custom-07131C white--text">
								{{ $t("profilePage.key") }}
							</th>
							<th class="t-bg-custom-07131C white--text t-text-center">
								{{ $t("profilePage.status") }}
							</th>
							<th class="t-bg-custom-07131C white--text t-text-center">
								{{ $t("profilePage.lastAccess") }}
							</th>
							<th class="t-bg-custom-07131C white--text t-text-center">
								<v-btn
									type="submit"
									color="#EBA900"
									class="t-uppercase t-mx-auto t-bg-[#03A593]"
									small
									@click="add_key()"
									>{{ $t("profilePage.create") }}</v-btn
								>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(value, key) in keys"
							:key="key"
							class="
                hover:t-bg-[#03A593] hover:t-bg-opacity-10
                t-transition-all t-duration-300
                white--text
              "
						>
							<td v-if="check_token_show(value.token) > -1">
								<div
									class="
                    t-grid
                    t-grid-flow-col
                    t-auto-cols-max
                    t-gap-1
                    t-items-center
                  "
								>
									<span class="t-select-all">{{ value.token }}</span>
									<v-btn
										icon
										color="white"
										x-small
										@click="toggle_token(value.token)"
									>
										<v-icon>mdi-eye-off</v-icon>
									</v-btn>
									<v-btn icon color="white" x-small @click="$copy(value.token)">
										<v-icon>mdi-content-copy</v-icon>
									</v-btn>
								</div>
							</td>
							<td
								v-else
								class="
                  t-grid t-grid-flow-col t-auto-cols-max t-gap-1 t-items-center
                "
							>
								<div>
									<v-icon v-for="i in 5" :key="i" color="white"
										>mdi-dots-horizontal</v-icon
									>
								</div>
								<v-btn
									icon
									color="white"
									x-small
									@click="toggle_token(value.token)"
								>
									<v-icon>mdi-eye</v-icon>
								</v-btn>
							</td>
							<td class="t-text-center">
								<v-switch
									:false-value="0"
									:true-value="1"
									:input-value="value.status"
									@click="edit_key(value.id)"
									color="#03A593"
									class="mx-auto t-inline-block"
								></v-switch>
							</td>
							<td class="t-text-center">{{ value.last_access }}</td>
							<td class="t-text-center">
								<v-btn
									type="submit"
									class="t-uppercase t-mx-auto"
									small
									@click="remove_key(value.id)"
									>{{ $t("profilePage.remove") }}</v-btn
								>
							</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table>
		</v-card>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data: () => ({
		passChange: {
			current_password: "",
			password: "",
			password_confirmation: "",
		},
		token_show: [],
	}),
	methods: {
		...mapActions("user", [
			"CHANGE_PASSWORD",
			"postUploadAvatar",
			"TOKEN_KEYS",
			"DELETE_TOKEN",
			"EDIT_TOKEN",
			"ADD_TOKEN",
		]),
		async postChangePassword() {
			const data = {
				current_password: this.passChange.current_password,
				password: this.passChange.password,
				password_confirmation: this.passChange.password_confirmation,
			};
			await this.CHANGE_PASSWORD({
				data,
				callback: async () => {
					this.passChange.current_password = "";
					this.passChange.password = "";
					this.passChange.password_confirmation = "";
				},
			});
		},
		async edit_key(id) {
			await this.EDIT_TOKEN(id);
			await this.TOKEN_KEYS();
		},
		async remove_key(id) {
			await this.DELETE_TOKEN(id);
			await this.TOKEN_KEYS();
		},
		add_key() {
			this.ADD_TOKEN();
			this.TOKEN_KEYS();
		},
		check_token_show(token) {
			return this.token_show.indexOf(token);
		},
		toggle_token(token) {
			if (this.check_token_show(token) > -1) {
				this.token_show.splice(this.token_show.indexOf(token), 1);
			} else {
				this.token_show.push(token);
			}
		},
	},
	computed: {
		...mapState("user", ["keys"]),
	},
	async mounted() {
		await this.TOKEN_KEYS();
	},
};
</script>

<style></style>
