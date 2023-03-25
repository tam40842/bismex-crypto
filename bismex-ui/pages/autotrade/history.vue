<template>
	<!-- <v-card
		color="#19232A"
		class="t-border t-border-[#03A593] t-max-w-full t-overflow-x-auto"
		id="history"
	>
		<div>
			<v-simple-table class="t-bg-transparent">
				<template v-slot:default>
					<thead>
						<tr class="t-bg-[#03A593] white--text">
							<th class="white--text t-text-center">Date</th>
							<th class="white--text t-text-center">Package ID</th>
							<th class="white--text t-text-center">Type</th>
							<th class="white--text t-text-center">Received</th>
							<th class="white--text t-text-center">Status</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(v, i) in histories.data"
							:key="i"
							class="hover:t-bg-[#03A593] hover:t-bg-opacity-10 t-transition-all t-duration-300 t-cursor-pointer"
							:class="[i % 2 ? 't-text-[#03A593]' : 't-text-white']"
						>
							<td class="t-text-center">{{ v.created_at }}</td>
							<td class="t-text-center">
								{{ v.package_id }}
							</td>
							<td class="t-text-center">{{ v.commission_type }}</td>
							<td class="t-text-center">
								${{ v.amount | displayCurrency(2) }}
							</td>
							<td class="t-text-center" v-if="v.package_status == 0">
								CANCELED
							</td>
							<td class="t-text-center" v-else-if="v.package_status == 1">
								RUNNING
							</td>
							<td class="t-text-center" v-else>COMPLETED</td>
						</tr>
					</tbody>
				</template>
			</v-simple-table>
		</div>
		<v-card-actions
			style="background: #03A593"
			class="t-px-4 t-border-t t-border-[#03A593] pt-3 t-border-opacity-20"
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

					<span class="t-capitalize white--text t-text-xs mt-1">Back</span>
				</div>
			</v-btn>

			<div>
				<div>
					<div class="d-flex flex-row align-center">
						<div class="d-flex flex-row align-center">
							<span class="white--text mx-2 t-text-xs">From</span>
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
									<v-btn text color="primary" @click="date_popup_from = false">
										Cancel
									</v-btn>
									<v-btn
										text
										color="primary"
										@click="$refs.date_from.save(date_from)"
									>
										OK
									</v-btn>
								</v-date-picker>
							</v-dialog>
						</div>

						<div class="d-flex flex-row align-center">
							<span class="white--text mx-2 t-text-xs">To</span>
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
									<v-btn text color="primary" @click="date_popup_to = false">
										Cancel
									</v-btn>
									<v-btn
										text
										color="primary"
										@click="$refs.date_to.save(date_to)"
									>
										OK
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
				:value="histories.current_page"
				:length="histories.last_page"
				@next="
					HISTORIES({
						page: histories.current_page + 1,
						date_from: date_from,
						date_to: date_to,
					})
				"
				@previous="
					HISTORIES({
						page: histories.current_page - 1,
						date_from: date_from,
						date_to: date_to,
					})
				"
				@input="
					(v) => HISTORIES({ page: v, date_from: date_from, date_to: date_to })
				"
				total-visible="7"
				color="#03A593"
			></v-pagination>
		</v-card-actions>
	</v-card> -->
	<div class="container manager-table">
		<v-data-table
			:headers="headers"
			:items="histories.data"
			item-key="text"
			class="elevation-1"
			hide-default-footer
			id="history"
		>
			<template v-slot:item.package_status="{ item }">
				<div class="t-text-center" v-if="item.package_status == 0">
					CANCELED
				</div>
				<div class="t-text-center" v-else-if="item.package_status == 1">
					RUNNING
				</div>
				<div class="t-text-center" v-else>COMPLETED</div>
			</template>
			<template v-slot:footer>
				<div class="d-flex flex-row align-center t-fl t-bg-custom-03A593 t-p-4">
					<v-row align="center">
						<v-col
							cols="12"
							md="6"
							class="t-flex t-justify-between manager-width"
						>
							<div class="d-flex flex-row align-center ">
								<span class="white--text mx-2 t-text-base t-mr-4">{{
									$t("commissionPage.from")
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
								<span class="white--text mx-2 t-text-base">{{
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
										<v-btn text color="primary" @click="date_popup_to = false">
											{{ $t("commissionPage.cancel") }}
										</v-btn>
										<v-btn
											text
											color="primary"
											@click="$refs.date_to.save(date_to)"
										>
											{{ $t("commissionPage.franchise") }}
										</v-btn>
									</v-date-picker>
								</v-dialog>
							</div>
						</v-col>
						<v-col md="3" class="t-hidden md:t-block"></v-col>
						<v-col
							cols="12"
							md="3"
							class="t-flex t-justify-center md:t-justify-end  md:t-jus "
						>
							<v-pagination
								v-model="page"
								:value="histories.current_page"
								:length="histories.last_page"
								@next="
									HISTORIES({
										page: histories.current_page + 1,
										date_from: date_from,
										date_to: date_to,
									})
								"
								@previous="
									HISTORIES({
										page: histories.current_page - 1,
										date_from: date_from,
										date_to: date_to,
									})
								"
								@input="
									(v) =>
										HISTORIES({
											page: v,
											date_from: date_from,
											date_to: date_to,
										})
								"
								total-visible="7"
								color="#03A593"
							></v-pagination>
						</v-col>
					</v-row>
					<div class="t-flex t-self-end"></div>
				</div>
			</template>
		</v-data-table>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			moreDetails: false,
			date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
				.toISOString()
				.substr(0, 10),
			menu: false,
			modal: false,
			symbol: null,
			page: 1,
			date_popup_from: false,
			date_popup_to: false,
			date_from: null,
			date_to: null,
			headers: [
				{ text: "Date", value: "created_at" },
				{ text: "Package ID", value: "package_id" },
				{ text: "Type", value: "commission_type" },
				{ text: "Received", value: "amount" },
				{ text: "Status", value: "package_status" },
			],
		};
	},
	methods: {
		...mapActions("autotrade", ["HISTORIES"]),
		postHistories() {
			const data = {
				page: this.page,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES(data);
		},
		searchHistories() {
			const data = {
				page: 1,
				date_from: this.date_from,
				date_to: this.date_to,
			};
			this.HISTORIES(data);
		},
	},
	computed: {
		...mapState("autotrade", ["histories"]),
	},
	async mounted() {
		await this.postHistories();
	},
};
</script>
<style lang="scss">
#history {
	.v-input__slot {
		min-height: 20px;
		box-shadow: none !important;
	}
	.v-input__control {
		min-height: 0;
	}
}
</style>
