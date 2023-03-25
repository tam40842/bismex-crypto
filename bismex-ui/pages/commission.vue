<template>
	<div>
		<v-container
			v-if="!$auth.user.is_franchise"
			class="
        t-grid t-grid-flow-row t-auto-rows-max t-gap-4
        lg:t-p-5
        t-p-3 t-mx-auto
      "
		>
			<v-card
				color="#19232A"
				class="
          t-p-5
          t-grid
          t-grid-flow-row
          t-auto-rows-max
          t-gap-4
          t-max-w-md
          t-border
          t-border-custom-EFEFEF
          t-rounded-md
          mx-auto
        "
			>
				<v-form @submit.prevent="" :readonly="$load('franchise')">
					<v-card-title class="pt-0">
						<h3 class="white--text t-font-bold t-mx-auto t-uppercase t-text-sm">
							{{ $t("commissionPage.franchise") }}
						</h3>
					</v-card-title>
					<v-card-text class="text-center pa-0">
						<h2
							class="
                white--text
                t-font-bold t-mx-auto t-uppercase t-text-2xl t-pb-6
              "
						>
							$50
						</h2>
						<v-divider dark class="t-p-3"></v-divider>

						<div class="text-left t-text-sm t-text-gray-300">
							{{ $t("commissionPage.yourBenefit") }}:

							<ul
								class="t-list-disc xl:t-text-base t-text-sm t-leading-4"
								style="list-style-type: disclosure-open !important"
							>
								<li class="t-py-2">
									<ul class="t-list-disc xl:t-text-base t-text-sm t-leading-4">
										{{
											$t("commissionPage.tradingVolume")
										}}:
										<li>
											{{ $t("commissionPage.fromSystem") }}
										</li>
										<li>{{ $t("commissionPage.fromFranchise") }}</li>
									</ul>
								</li>
								<li class="t-py-2">
									{{ $t("commissionPage.condition") }}:
									<ul class="t-list-disc xl:t-text-base t-text-sm t-leading-4">
										<li>
											{{ $t("commissionPage.maintain") }}
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</v-card-text>
					<v-card-actions>
						<v-btn
							color="#03A593"
							type="submit"
							class="capitalize t-mt-10 white--text t-mx-auto white--text"
							:loading="$load('franchise')"
							@click="JOIN_FRANCHISE()"
							width="120"
						>
							{{ $t("commissionPage.buy") }}
						</v-btn>
					</v-card-actions>
				</v-form>
			</v-card>
		</v-container>
		<v-container
			v-else
			class="

      "
		>
			<div class="t-block">
				<TabsRouter class="lg:t-w-2/5 t-mx-auto t-py-5" :router="router" />
			</div>
			<NuxtChild />
		</v-container>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
	layout: "board",
	// middleware({ store, redirect }) {
	// 	if (!store.$auth.user.is_commission) {
	// 		return redirect("/trading");
	// 	}
	// },
	data() {
		return {};
	},
	methods: {
		...mapActions("franchise", ["ACTIVE_FRANCHISE"]),
		JOIN_FRANCHISE() {
			this.ACTIVE_FRANCHISE();
		},
	},
	computed: {
		router() {
			return [
				{
					name: this.$t("commissionPage.overview"),
					path: "/commission/overview",
				},
				{
					name: this.$t("commissionPage.manager"),
					path: "/commission/manager",
				},
			];
		},
	},
};
</script>

<style></style>
