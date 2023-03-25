<template>
	<div
		class="
			t-grid t-grid-flow-row t-auto-rows-max t-gap-4
			lg:t-p-5
			t-p-3 t-mx-auto t-w-full
		"
	>
		<v-card color="#19232A" class="t-border t-border-custom-EFEFEF t-p-4">
			<v-card-text class="t-flex t-flex-col">
				<label class="white--text t-mb-3 t-font-bold">
					{{ $t("memberPage.registrationLink") }}
				</label>
				<v-text-field
					solo
					hide-details
					color="#03A593"
					append-icon="mdi-content-copy"
					:value="ref_url"
				>
					<template v-slot:append>
						<v-btn icon color="#03A593" @click="$copy(ref_url)">
							<svg
								width="20"
								height="24"
								viewBox="0 0 20 24"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
							>
								<path
									d="M14.4 0.0232647C14.315 0.0232647 6.17142 0 6.17142 0C4.72044 0 3.42858 1.45576 3.42858 3.00002L2.63589 3.02026C1.1856 3.02026 0 4.45574 0 6V21C0 22.5443 1.29189 24 2.74287 24H13.0286C14.4795 24 15.7714 22.5443 15.7714 21H16.4571C17.9081 21 19.2 19.5443 19.2 18V6.01801L14.4 0.0232647ZM13.0286 22.5H2.74287C2.02287 22.5 1.37145 21.7643 1.37145 21V6C1.37145 4.88401 2.21076 4.50001 3.42858 4.50001V18C3.42858 19.5443 4.72044 21 6.17142 21C6.17142 21 13.6059 20.9918 14.4061 20.9918C14.4061 21.9023 13.8604 22.5 13.0286 22.5ZM14.4 15.7582H8.22858C7.85007 15.7582 7.54287 15.423 7.54287 15.009C7.54287 14.595 7.85007 14.2598 8.22858 14.2598H14.4C14.7785 14.2598 15.0857 14.595 15.0857 15.009C15.0857 15.423 14.7785 15.7582 14.4 15.7582ZM14.4 12.012H8.22858C7.85007 12.012 7.54287 11.6767 7.54287 11.2628C7.54287 10.8488 7.85007 10.5135 8.22858 10.5135H14.4C14.7785 10.5135 15.0857 10.8488 15.0857 11.2628C15.0857 11.676 14.7785 12.012 14.4 12.012ZM15.7714 6C15.0412 6 14.4 5.27927 14.4 4.50001C14.4 4.50001 14.4 2.98049 14.4 1.52326V1.52175L17.8286 6H15.7714Z"
									fill="#03A593"
								/>
							</svg>
						</v-btn> </template
				></v-text-field>
			</v-card-text>
		</v-card>
		<v-card
			color="#19232A"
			class="
				t-border t-border-custom-EFEFEF t-p-4 t-max-w-full t-overflow-x-hidden
			"
		>
			<v-card-title class="t-py-0">
				<h3 class="mx-auto t-capitalize t-text-custom-FFE600 t-font-bold">
					Volume F1
				</h3>
			</v-card-title>
			<v-card-text
				class="max-h-screen t-overflow-auto"
				style="max-height: 400px"
				>chart</v-card-text
			>
			<v-card-actions
				class="t-block t-text-center xl:t-text-3xl t-font-bold white--text"
			>
				{{ $t("memberPage.total") }}: $300,000</v-card-actions
			>
		</v-card>
	</div>
</template>

<script>
export default {
	data() {
		return {
			option: {
				tree: false,
			},
		};
	},
	computed: {
		ref_url() {
			return window.location.origin + "/register?ref=" + this.$auth.user.ref_id;
		},
	},
};
</script>

<style></style>
