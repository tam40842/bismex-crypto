<template>
	<div class="">
		<div
			class="t-grid xl:t-grid-cols-5 t-grid-cols-1 t-gap-4"
			v-if="commission"
		>
			<div class="t-h-full">
				<v-card
					color="#19232A"
					elevation="0"
					class="
            t-border-custom-03A593 t-flex t-flex-col t-text-center t-h-full
          "
				>
					<div
						class="
              t-flex
              t-flex-col
              t-justify-between

              t-pt-10
              t-pb-2
              t-left-commission
              t-flex-grow
            "
					>
						<div
							class="
                t-flex
                t-flex-col
                t-text-center
                t-text-white
                t-font-bold
                t-text-2xl
                t-mb-2
              "
						>
							{{ $t("commissionPage.totalIB") }}
						</div>
						<div
							class="
                t-flex
                t-flex-col
                t-text-center
                t-text-white
                t-font-bold
                t-text-5xl
                t-mb-5
              "
						>
							$
							{{
								(commission.com_today + commission.franchise_today)
									| displayCurrency(2)
							}}
						</div>
						<div class="t-flex t-flex-row t-justify-between">
							<div class="t-flex t-flex-col t-py-10">
								<div class="t-text-lg t-text-white t-font-bold t-text-center">
									{{ $t("commissionPage.ibCommission") }}
								</div>
								<div class="t-text-lg t-text-white t-font-bold t-text-center">
									$ {{ commission.com_today | displayCurrency(2) }}
								</div>
							</div>
							<div class="t-flex t-flex-col t-py-10">
								<div class="t-text-lg t-text-white t-font-bold t-text-center">
									{{ $t("commissionPage.ibFranchise") }}
								</div>
								<div class="t-text-lg t-text-white t-font-bold t-text-center">
									$ {{ commission.franchise_today | displayCurrency(2) }}
								</div>
							</div>
						</div>
					</div>
				</v-card>
			</div>
			<div
				class="
          xl:t-col-span-4
          t-grid t-gap-4
          xl:t-grid-cols-4
          sm:t-grid-cols-2
          t-grid-cols-1
        "
			>
				<v-card
					color="#19232A"
					elevation="0"
					class="
            t-border-custom-03A593 t-text-center t-col-span-1 t-flex t-flex-col
          "
				>
					<v-card-title
						class="t-font-bold t-block t-text-custom-EFEFEF t-text-lg"
					>
						<div>{{ $t("commissionPage.today") }}</div>
						<div>
							${{
								(commission.com_subday + commission.franchise_subday)
									| displayCurrency(2)
							}}
						</div>
					</v-card-title>
					<div class="t-px-4">
						<v-divider class="t-border-custom-EFEFEF t-border-opacity-40" />
					</div>
					<v-card-text
						class="t-flex-grow t-flex t-flex-col t-justify-between t-h-full"
					>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibCommission") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.com_subday | displayCurrency(2) }}
							</v-card-title>
						</div>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibFranchise") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.franchise_subday | displayCurrency(2) }}
							</v-card-title>
						</div>
					</v-card-text>
				</v-card>
				<v-card
					color="#19232A"
					elevation="0"
					class="
            t-border-custom-03A593 t-text-center t-col-span-1 t-flex t-flex-col
          "
				>
					<v-card-title
						class="t-font-bold t-block t-text-custom-EFEFEF t-text-lg"
					>
						<div>{{ $t("commissionPage.yesterday") }}</div>
						<div>
							${{
								(commission.com_two_day + commission.franchise_two_day)
									| displayCurrency(2)
							}}
						</div>
					</v-card-title>
					<div class="t-px-4">
						<v-divider class="t-border-custom-EFEFEF t-border-opacity-40" />
					</div>
					<v-card-text
						class="t-flex-grow t-flex t-flex-col t-justify-between t-h-full"
					>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibCommission") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.com_two_day | displayCurrency(2) }}
							</v-card-title>
						</div>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibFranchise") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.franchise_two_day | displayCurrency(2) }}
							</v-card-title>
						</div>
					</v-card-text>
				</v-card>
				<v-card
					color="#19232A"
					elevation="0"
					class="
            t-border-custom-03A593 t-text-center t-col-span-1 t-flex t-flex-col
          "
				>
					<v-card-title
						class="t-font-bold t-block t-text-custom-EFEFEF t-text-lg"
					>
						<div>{{ $t("commissionPage.month") }}</div>
						<div>
							${{
								(commission.com_month + commission.franchise_month)
									| displayCurrency(2)
							}}
						</div>
					</v-card-title>
					<div class="t-px-4">
						<v-divider class="t-border-custom-EFEFEF t-border-opacity-40" />
					</div>
					<v-card-text
						class="t-flex-grow t-flex t-flex-col t-justify-between t-h-full"
					>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibCommission") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.com_month | displayCurrency(2) }}
							</v-card-title>
						</div>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibFranchise") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.franchise_month | displayCurrency(2) }}
							</v-card-title>
						</div>
					</v-card-text>
				</v-card>
				<v-card
					color="#19232A"
					elevation="0"
					class="
            t-border-custom-03A593 t-text-center t-col-span-1 t-flex t-flex-col
          "
				>
					<v-card-title
						class="t-font-bold t-block t-text-custom-EFEFEF t-text-lg"
					>
						<div>{{ $t("commissionPage.total") }}</div>
						<div>
							${{
								(commission.total_commission + commission.total_franchise)
									| displayCurrency(2)
							}}
						</div>
					</v-card-title>
					<div class="t-px-4">
						<v-divider class="t-border-custom-EFEFEF t-border-opacity-40" />
					</div>
					<v-card-text
						class="t-flex-grow t-flex t-flex-col t-justify-between t-h-full"
					>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibCommission") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.total_commission | displayCurrency(2) }}
							</v-card-title>
						</div>
						<div class="t-block">
							<v-card-subtitle
								class="t-pb-0 t-text-custom-EFEFEF t-text-opacity-50"
							>
								{{ $t("commissionPage.ibFranchise") }}
							</v-card-subtitle>
							<v-card-title
								class="t-font-bold t-block t-text-custom-EFEFEF t-pt-0"
							>
								${{ commission.total_franchise | displayCurrency(2) }}
							</v-card-title>
						</div>
					</v-card-text>
				</v-card>
			</div>
		</div>
		<div class="t-flex t-flex-col t-mt-5">
			<v-card
				color="#19232A"
				class="t-border t-border-custom-03A593 t-max-w-full t-overflow-x-auto"
			>
				<v-simple-table class="t-bg-transparent">
					<template v-slot:default>
						<thead>
							<tr class="t-bg-[#00695d] white--text">
								<th class="white--text t-text-center">
									{{ $t("commissionPage.date") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("commissionPage.ibCommission") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("commissionPage.ibFranchise") }}
								</th>
								<th class="white--text t-text-center">
									{{ $t("commissionPage.total") }}
								</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(v, i) in histories_days.data"
								:key="i"
								class="
                  hover:t-bg-[#153f41]
                  t-bg-[#19232a]
                  t-transition-all t-duration-300
                "
								:class="[i % 2 ? 't-text-custom-03A593' : 't-text-white']"
							>
								<td class="t-text-center">{{ v.date_com }}</td>
								<td class="t-text-center">${{ v.com | displayCurrency(2) }}</td>
								<td class="t-text-center">
									${{ v.ib_franchise | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ (v.ib_franchise + v.com) | displayCurrency(2) }}
								</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
				<v-card-actions
					style="background: #03A593"
					class="
            t-px-4 t-border-t t-border-custom-EFEFEF
            pt-3
            t-border-opacity-20
          "
				>
					<v-btn text depressed @click="moreDetails = false" class="mr-5">
						<div class="d-flex align-center">
							<svg
								class="mx-2"
								width="15"
								height="15"
								viewBox="0 0 15 15"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
							>
								<path
									d="M10 13L5 8L10 3"
									stroke="white"
									stroke-linecap="round"
									stroke-linejoin="round"
								/>
							</svg>

							<span class="t-capitalize white--text t-text-xs mt-1">{{
								$t("commissionPage.back")
							}}</span>
						</div>
					</v-btn>

					<div>
						<div>
							<div class="d-flex flex-row align-center">
								<div class="d-flex flex-row align-center">
									<span class="white--text mx-2 t-text-xs">{{
										$t("commissionPage.yesterday")
									}}</span>
									<v-dialog
										ref="date_from"
										v-model="date_popup_from"
										:return-value.sync="date_from"
										persistent
										width="290px"
									>
										<template v-slot:activator="{ on, attrs }">
											<v-text-field
												v-model="date_from"
												solo
												hide-details
												width="150px"
												readonly
												v-bind="attrs"
												v-on="on"
											>
												<template v-slot:append>
													<svg
														width="15"
														class="mx-2 pointer"
														height="15"
														viewBox="0 0 15 15"
														fill="none"
														xmlns="http://www.w3.org/2000/svg"
													>
														<path
															d="M13.5 0H1.5C0.675 0 0 0.6755 0 1.5V13.5C0 14.324 0.675 15 1.5 15H13.5C14.325 15 15 14.324 15 13.5V1.5C15 0.6755 14.325 0 13.5 0ZM4.048 12.179C4.048 12.7434 3.59044 13.201 3.026 13.201H2.772C2.20757 13.201 1.75 12.7434 1.75 12.179C1.75 11.6146 2.20756 11.157 2.772 11.157H3.026C3.59043 11.157 4.048 11.6146 4.048 12.179ZM4.048 9.45C4.048 10.0142 3.59066 10.4715 3.0265 10.4715H2.7715C2.20734 10.4715 1.75 10.0142 1.75 9.45C1.75 8.88584 2.20734 8.4285 2.7715 8.4285H3.0265C3.59066 8.4285 4.048 8.88584 4.048 9.45ZM3.875 2.2735C3.5235 2.2735 3.2385 1.989 3.2385 1.637C3.2385 1.285 3.5235 1 3.875 1C4.2265 1 4.5115 1.2845 4.5115 1.6365C4.5115 1.9885 4.2265 2.2735 3.875 2.2735ZM7.1145 12.179C7.1145 12.7434 6.65694 13.201 6.0925 13.201H5.839C5.27456 13.201 4.817 12.7434 4.817 12.179C4.817 11.6146 5.27456 11.157 5.839 11.157H6.0925C6.65694 11.157 7.1145 11.6146 7.1145 12.179ZM7.1145 9.45C7.1145 10.0142 6.65716 10.4715 6.093 10.4715H5.8385C5.27434 10.4715 4.817 10.0142 4.817 9.45C4.817 8.88584 5.27434 8.4285 5.8385 8.4285H6.093C6.65716 8.4285 7.1145 8.88584 7.1145 9.45ZM7.1145 6.7225C7.1145 7.28666 6.65716 7.744 6.093 7.744H5.8385C5.27434 7.744 4.817 7.28666 4.817 6.7225C4.817 6.15834 5.27434 5.701 5.8385 5.701H6.093C6.65716 5.701 7.1145 6.15834 7.1145 6.7225ZM6.8635 1.6365C6.8635 1.2845 7.1485 1 7.5 1C7.8515 1 8.1365 1.2845 8.1365 1.6365C8.1365 1.9885 7.8515 2.273 7.5 2.273C7.1485 2.273 6.8635 1.989 6.8635 1.6365ZM10.183 10.4715H8.9065C8.34234 10.4715 7.885 10.0142 7.885 9.45C7.885 8.88584 8.34234 8.4285 8.9065 8.4285H9.162C9.72616 8.4285 10.1835 8.88584 10.1835 9.45V10.471C10.1835 10.4713 10.1833 10.4715 10.183 10.4715ZM10.183 7.744H8.9065C8.34234 7.744 7.885 7.28666 7.885 6.7225C7.885 6.15834 8.34234 5.701 8.9065 5.701H9.162C9.72616 5.701 10.1835 6.15834 10.1835 6.7225V7.7435C10.1835 7.74378 10.1833 7.744 10.183 7.744ZM10.4885 1.6365C10.4885 1.2845 10.7735 1 11.125 1C11.4765 1 11.7615 1.2845 11.7615 1.6365C11.7615 1.9885 11.4765 2.273 11.125 2.273C10.7735 2.273 10.4885 1.989 10.4885 1.6365ZM13.25 9.45C13.25 10.0142 12.7927 10.4715 12.2285 10.4715H11.9735C11.4093 10.4715 10.952 10.0142 10.952 9.45C10.952 8.88584 11.4093 8.4285 11.9735 8.4285H12.2285C12.7927 8.4285 13.25 8.88584 13.25 9.45ZM13.25 6.7225C13.25 7.28666 12.7927 7.744 12.2285 7.744H11.9735C11.4093 7.744 10.952 7.28666 10.952 6.7225C10.952 6.15834 11.4093 5.701 11.9735 5.701H12.2285C12.7927 5.701 13.25 6.15834 13.25 6.7225Z"
															fill="#374957"
														/>
													</svg>
												</template>
											</v-text-field>
										</template>
										<v-date-picker
											v-model="date_from"
											scrollable
											@change="searchHistories()"
										>
											<v-spacer></v-spacer>
											<v-btn
												text
												color="primary"
												@click="date_popup_from = false"
											>
												{{ $t("commissionPage.cancel") }}
											</v-btn>
											<v-btn
												text
												color="primary"
												@click="$refs.date_from.save(date_from)"
											>
												{{ $t("commissionPage.ok") }}
											</v-btn>
										</v-date-picker>
									</v-dialog>
								</div>

								<div class="d-flex flex-row align-center">
									<span class="white--text mx-2 t-text-xs">{{
										$t("commissionPage.to")
									}}</span>
									<v-dialog
										ref="date_to"
										v-model="date_popup_to"
										:return-value.sync="date_to"
										persistent
										width="290px"
									>
										<template v-slot:activator="{ on, attrs }">
											<v-text-field
												v-model="date_to"
												solo
												hide-details
												width="150px"
												readonly
												v-bind="attrs"
												v-on="on"
											>
												<template v-slot:append>
													<svg
														width="15"
														class="mx-2 pointer"
														height="15"
														viewBox="0 0 15 15"
														fill="none"
														xmlns="http://www.w3.org/2000/svg"
													>
														<path
															d="M13.5 0H1.5C0.675 0 0 0.6755 0 1.5V13.5C0 14.324 0.675 15 1.5 15H13.5C14.325 15 15 14.324 15 13.5V1.5C15 0.6755 14.325 0 13.5 0ZM4.048 12.179C4.048 12.7434 3.59044 13.201 3.026 13.201H2.772C2.20757 13.201 1.75 12.7434 1.75 12.179C1.75 11.6146 2.20756 11.157 2.772 11.157H3.026C3.59043 11.157 4.048 11.6146 4.048 12.179ZM4.048 9.45C4.048 10.0142 3.59066 10.4715 3.0265 10.4715H2.7715C2.20734 10.4715 1.75 10.0142 1.75 9.45C1.75 8.88584 2.20734 8.4285 2.7715 8.4285H3.0265C3.59066 8.4285 4.048 8.88584 4.048 9.45ZM3.875 2.2735C3.5235 2.2735 3.2385 1.989 3.2385 1.637C3.2385 1.285 3.5235 1 3.875 1C4.2265 1 4.5115 1.2845 4.5115 1.6365C4.5115 1.9885 4.2265 2.2735 3.875 2.2735ZM7.1145 12.179C7.1145 12.7434 6.65694 13.201 6.0925 13.201H5.839C5.27456 13.201 4.817 12.7434 4.817 12.179C4.817 11.6146 5.27456 11.157 5.839 11.157H6.0925C6.65694 11.157 7.1145 11.6146 7.1145 12.179ZM7.1145 9.45C7.1145 10.0142 6.65716 10.4715 6.093 10.4715H5.8385C5.27434 10.4715 4.817 10.0142 4.817 9.45C4.817 8.88584 5.27434 8.4285 5.8385 8.4285H6.093C6.65716 8.4285 7.1145 8.88584 7.1145 9.45ZM7.1145 6.7225C7.1145 7.28666 6.65716 7.744 6.093 7.744H5.8385C5.27434 7.744 4.817 7.28666 4.817 6.7225C4.817 6.15834 5.27434 5.701 5.8385 5.701H6.093C6.65716 5.701 7.1145 6.15834 7.1145 6.7225ZM6.8635 1.6365C6.8635 1.2845 7.1485 1 7.5 1C7.8515 1 8.1365 1.2845 8.1365 1.6365C8.1365 1.9885 7.8515 2.273 7.5 2.273C7.1485 2.273 6.8635 1.989 6.8635 1.6365ZM10.183 10.4715H8.9065C8.34234 10.4715 7.885 10.0142 7.885 9.45C7.885 8.88584 8.34234 8.4285 8.9065 8.4285H9.162C9.72616 8.4285 10.1835 8.88584 10.1835 9.45V10.471C10.1835 10.4713 10.1833 10.4715 10.183 10.4715ZM10.183 7.744H8.9065C8.34234 7.744 7.885 7.28666 7.885 6.7225C7.885 6.15834 8.34234 5.701 8.9065 5.701H9.162C9.72616 5.701 10.1835 6.15834 10.1835 6.7225V7.7435C10.1835 7.74378 10.1833 7.744 10.183 7.744ZM10.4885 1.6365C10.4885 1.2845 10.7735 1 11.125 1C11.4765 1 11.7615 1.2845 11.7615 1.6365C11.7615 1.9885 11.4765 2.273 11.125 2.273C10.7735 2.273 10.4885 1.989 10.4885 1.6365ZM13.25 9.45C13.25 10.0142 12.7927 10.4715 12.2285 10.4715H11.9735C11.4093 10.4715 10.952 10.0142 10.952 9.45C10.952 8.88584 11.4093 8.4285 11.9735 8.4285H12.2285C12.7927 8.4285 13.25 8.88584 13.25 9.45ZM13.25 6.7225C13.25 7.28666 12.7927 7.744 12.2285 7.744H11.9735C11.4093 7.744 10.952 7.28666 10.952 6.7225C10.952 6.15834 11.4093 5.701 11.9735 5.701H12.2285C12.7927 5.701 13.25 6.15834 13.25 6.7225Z"
															fill="#374957"
														/>
													</svg>
												</template>
											</v-text-field>
										</template>
										<v-date-picker
											v-model="date_to"
											scrollable
											@change="searchHistories()"
										>
											<v-spacer></v-spacer>
											<v-btn
												text
												color="primary"
												@click="date_popup_to = false"
											>
												{{ $t("commissionPage.cancel") }}
											</v-btn>
											<v-btn
												text
												color="primary"
												@click="$refs.date_to.save(date_to)"
											>
												{{ $t("commissionPage.ok") }}
											</v-btn>
										</v-date-picker>
									</v-dialog>
								</div>
							</div>
						</div>
					</div>
					<v-spacer></v-spacer>

					<v-pagination
						v-model="page"
						:value="histories_days.current_page"
						:length="histories_days.last_page"
						@next="
							HISTORIES_DAYS({
								page: histories_days.current_page + 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@previous="
							HISTORIES_DAYS({
								page: histories_days.current_page - 1,
								date_from: date_from,
								date_to: date_to,
							})
						"
						@input="
							(v) =>
								HISTORIES_DAYS({
									page: v,
									date_from: date_from,
									date_to: date_to,
								})
						"
						total-visible="7"
						color="#03A593"
					></v-pagination>
				</v-card-actions>
			</v-card>
		</div>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			page: 1,
			date_popup_from: false,
			date_popup_to: false,
			date_from: null,
			date_to: null,
		};
	},
	computed: {
		...mapState("franchise", ["commission", "histories_days"]),
	},
	methods: {
		...mapActions("franchise", ["Overview", "HISTORIES_DAYS"]),
		postHistories() {
			const data = {
				page: this.page,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_DAYS(data);
		},
		searchHistories() {
			const data = {
				page: 1,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES_DAYS(data);
		},
	},
	async mounted() {
		await this.Overview();
		await this.postHistories();
	},
};
</script>

<style lang="scss">
.t-left-commission {
	background-image: url("~/assets/images/global/hand-money.png");
	background-position: bottom right;
}
</style>
