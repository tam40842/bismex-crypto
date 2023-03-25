<template>
	<div>
		<v-card
			v-if="overview.package_detail == undefined"
			color="#19232A"
			class="
        t-p-5
        t-grid
        t-grid-flow-row
        t-auto-rows-max
        t-gap-4
        t-max-w-md
        t-border
        t-border-[#03A593]
        t-rounded-md
        mx-auto
      "
		>
			<v-form @submit.prevent="" :readonly="$load('autotrade')">
				<v-card-title class="pt-0">
					<h3 class="white--text t-font-bold t-mx-auto t-uppercase t-text-sm">
						{{ $t("autotradePage.autotradeFund") }}
					</h3>
				</v-card-title>
				<v-card-text class="text-center pa-0">
					<h2
						class="
              white--text
              t-font-bold t-mx-auto t-uppercase t-text-2xl t-pb-6
            "
					>
						$1000
					</h2>
					<v-divider dark class="t-p-3"></v-divider>

					<div class="text-left t-text-sm t-text-gray-300">
						{{ $t("autotradePage.yourBenefit") }}

						<ul class="t-list-disc xl:t-text-base t-text-sm t-leading-4">
							<li class="t-py-2">
								{{ $t("autotradePage.profit") }}:
								<span class="white--text font-weight-bold">3-10%/month</span>
							</li>
							<li class="t-py-2">
								{{ $t("autotradePage.interestReceiving") }}
								<span class="white--text font-weight-bold">{{
									$t("autotradePage.eachmonth")
								}}</span>
							</li>
							<li class="t-py-2">
								<span class="white--text font-weight-bold">{{
									$t("autotradePage.activateAuto")
								}}</span>
								{{ $t("autotradePage.botDaily") }}
							</li>
							<li class="t-py-2">
								{{ $t("autotradePage.canBorrow") }}
								<span class="white--text font-weight-bold">$300</span>
								{{ $t("autotradePage.with") }}
								<span class="white--text font-weight-bold">0%</span>
								{{ $t("autotradePage.interest24") }}
							</li>
						</ul>
					</div>
				</v-card-text>
				<v-card-actions>
					<v-btn
						color="#03A593"
						type="submit"
						class="capitalize t-mt-10 white--text t-mx-auto white--text"
						:loading="$load('autotrade')"
						@click="BUY_PACKAGE()"
						width="120"
						>{{ $t("autotradePage.buy") }}</v-btn
					>
				</v-card-actions>
			</v-form>
		</v-card>

		<div v-else>
			<v-row justify="center" class="row-maxWidth">
				<v-col cols="12" md="7" lg="7" xl="9">
					<v-card
						color="#19232A"
						class="
              t-p-5
              t-grid
              t-grid-flow-row
              t-auto-rows-max
              t-gap-4
              t-border
              t-border-[#03A593]
              t-rounded-md
              t-max-h-full
            "
					>
						<v-form @submit.prevent="" :readonly="$load('autotrade')">
							<v-row>
								<v-col cols="12" md="4" lg="4" xl="4" align="center">
									<v-card-title
										class="d-flex flex-column  t-items-center
						md:t-items-start py-0"
									>
										<span
											class="
                        grey--text
                        t-font-bold t-capitalize t-text-sm
                        d-flex

                      "
										>
											{{ $t("autotradePage.autoTradeBalance") }}
											<v-tooltip
												right
												color="#19232A"
												max-width="158px"
												class="pointer"
											>
												<template
													v-slot:activator="{
														on,
														attrs,
													}"
												>
													<div v-bind="attrs" v-on="on">
														<svg
															class="ml-2"
															width="16"
															height="16"
															viewBox="0 0 16 16"
															fill="none"
															xmlns="http://www.w3.org/2000/svg"
														>
															<path
																d="M8 14C11.3137 14 14 11.3137 14 8C14 4.68629 11.3137 2 8 2C4.68629 2 2 4.68629 2 8C2 11.3137 4.68629 14 8 14Z"
																stroke="#BCBCBC"
																stroke-linecap="round"
																stroke-linejoin="round"
															/>
															<path
																d="M7.5 7.5H8V11H8.5"
																stroke="#BCBCBC"
																stroke-linecap="round"
																stroke-linejoin="round"
															/>
															<path
																d="M8 6C8.41421 6 8.75 5.66421 8.75 5.25C8.75 4.83579 8.41421 4.5 8 4.5C7.58579 4.5 7.25 4.83579 7.25 5.25C7.25 5.66421 7.58579 6 8 6Z"
																fill="#BCBCBC"
															/>
														</svg>
													</div>
												</template>
												<span class="tooltipFont"
													>{{ $t("autotradePage.ifNot") }}
													<span class="white--text">$1000</span>
													{{ $t("autotradePage.before") }}
													<span class="white--text">00:00 UTC</span>,
													{{ $t("autotradePage.theFund") }}
												</span>
											</v-tooltip>
										</span>
										<h2
											class="
                        white--text
                        t-font-bold t-uppercase t-text-2xl t-pb-6
                      "
										>
											${{ $auth.user.autotrade_balance | displayCurrency(2) }}
										</h2>
									</v-card-title>

									<v-card-title
										class="d-flex flex-column  t-items-center
						md:t-items-start py-0"
									>
										<span
											class="grey--text t-font-bold t-capitalize t-text-sm"
											style="word-break: break-word"
										>
											{{ $t("autotradePage.interest") }}
											{{
												overview.package_detail.withdraw_status == 0
													? ""
													: overview.package_detail.withdraw_status == 1
													? "PENDING"
													: overview.package_detail.withdraw_status == 2
													? "APPROVED"
													: "CANCELLED"
											}}
										</span>
										<h2 class="white--text t-font-bold t-uppercase t-text-lg">
											${{
												overview.package_detail.received | displayCurrency(2)
											}}
										</h2>
										<v-dialog
											v-if="overview.package_detail.received > 0"
											v-model="withdrawDialog"
											width="288"
											persistent
										>
											<template v-slot:activator="{ on, attrs }">
												<v-btn
													:disabled="
														overview.package_detail.withdraw_status == 1
													"
													width="120px"
													height="38px"
													dark
													v-bind="attrs"
													v-on="on"
													depressed
													color="#03A593"
												>
													<span
														v-if="overview.package_detail.withdraw_status == 1"
														class="t-capitalize t-text-xl"
														>{{ $t("autotradePage.waiting") }}</span
													>
													<span v-else class="t-capitalize t-text-xl">{{
														$t("autotradePage.get")
													}}</span>
												</v-btn>
											</template>

											<v-card color="#19232A">
												<v-card-text class="pa-5">
													<label class="grey--text t-mb-3">{{
														$t("autotradePage.availableWith")
													}}</label>
													<h2
														class="
                              white--text
                              t-font-bold t-uppercase t-text-2xl t-pb-6
                            "
													>
														${{
															(overview.package_detail.received -
																overview.package_detail.withdraw_complete)
																| displayCurrency(2)
														}}
													</h2>

													<v-text-field
														v-model="amountWithdraw"
														solo
														hide-details
														class="my-5"
														placeholder="Amount"
													/>
												</v-card-text>

												<v-card-actions>
													<v-spacer></v-spacer>
													<v-btn
														width="120px"
														height="38px"
														dark
														depressed
														color="#515252"
														@click="withdrawDialog = false"
													>
														<span
															class="t-capitalize t-text-xl t-text-gray-300"
															>{{ $t("autotradePage.cancel") }}</span
														>
													</v-btn>

													<v-btn
														width="120px"
														height="38px"
														dark
														depressed
														color="#03A593"
														@click="
															GET({
																amount: amountWithdraw,
																callback: () => {
																	withdrawDialog = false;
																	amountWithdraw = 0;
																},
															})
														"
													>
														<span class="t-capitalize t-text-xl">{{
															$t("autotradePage.get")
														}}</span>
													</v-btn>
													<v-spacer></v-spacer>
												</v-card-actions>
											</v-card>
										</v-dialog>
									</v-card-title>
								</v-col>

								<v-col cols="12" md="4" lg="4" xl="4" align-self="end">
									<v-card-title
										class="d-flex flex-column t-items-center
						md:t-items-start py-0"
									>
										<span class="grey--text t-font-bold t-capitalize t-text-sm">
											{{ $t("autotradePage.profit") }}
										</span>
										<h2 class="white--text t-font-bold t-text-lg">
											${{ overview.profit | displayCurrency(2) }}/{{
												$t("autotradePage.day")
											}}
										</h2>
									</v-card-title>
								</v-col>

								<v-col
									cols="12"
									md="4"
									lg="4"
									xl="4"
									align-self="center"
									align="right"
								>
									<v-dialog v-model="swapDialog" width="289" persistent>
										<template v-slot:activator="{ on, attrs }">
											<v-btn
												width="120px"
												height="38px"
												dark
												depressed
												color="transparent"
												v-bind="attrs"
												v-on="on"
											>
												<span class="t-capitalize t-text-2xl">{{
													$t("autotradePage.swap")
												}}</span>
											</v-btn>
										</template>

										<v-card max-width="289" color="#19232A">
											<v-card-text class="pa-5">
												<v-text-field
													:value="$auth.user.autotrade_balance"
													dark
													outlined
													hide-details
													label="Auto Trade Balance"
												/>

												<svg
													width="24"
													height="24"
													viewBox="0 0 24 24"
													class="mx-auto my-5"
													fill="none"
													xmlns="http://www.w3.org/2000/svg"
												>
													<path
														d="M19.5 12L12 19.5L4.5 12"
														stroke="white"
														stroke-width="1.5"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
													<path
														d="M19.5 4.5L12 12L4.5 4.5"
														stroke="white"
														stroke-width="1.5"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
												</svg>

												<v-text-field
													:value="$auth.user.autotrade_balance"
													dark
													outlined
													hide-details
													label="Available Balance"
												/>

												<v-text-field
													v-model="amountSwap"
													solo
													hide-details
													class="my-7"
													placeholder="Amount"
												/>
											</v-card-text>

											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#515252"
													@click="swapDialog = false"
												>
													<span class="t-capitalize white--text t-text-xl">{{
														$t("autotradePage.cancel")
													}}</span>
												</v-btn>

												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#03A593"
													@click="
														Swap({
															amount: amountSwap,
															callback: () => {
																swapDialog = false;
																amountSwap = 0;
															},
														})
													"
												>
													<span class="t-capitalize t-text-2xl">{{
														$t("autotradePage.swap")
													}}</span>
												</v-btn>
												<v-spacer></v-spacer>
											</v-card-actions>
										</v-card>
									</v-dialog>
								</v-col>
							</v-row>
						</v-form>
					</v-card>

					<v-row dense>
						<v-col cols="12" md="12" lg="6" xl="6" align-self="center">
							<v-card
								color="#19232A"
								class="
                  t-p-5
                  t-grid
                  t-grid-flow-row
                  t-auto-rows-max
                  t-gap-4
                  t-border
                 t-border-[#03A593]
                  t-rounded-md
                  my-5
                "
							>
								<v-card-title class="pa-0">
									<v-spacer />
									<span
										class="grey--text t-font-bold t-capitalize t-text-sm pl-5"
									>
										{{ $t("autotradePage.borrowed") }}
									</span>
									<v-spacer />

									<v-tooltip
										v-if="overview.package_detail.borrow_amount"
										right
										color="#19232A"
										max-width="158px"
										class="pointer"
									>
										<template v-slot:activator="{ on, attrs }">
											<div v-bind="attrs" v-on="on">
												<svg
													width="16"
													height="16"
													viewBox="0 0 16 16"
													fill="none"
													xmlns="http://www.w3.org/2000/svg"
												>
													<path
														d="M8 14C11.3137 14 14 11.3137 14 8C14 4.68629 11.3137 2 8 2C4.68629 2 2 4.68629 2 8C2 11.3137 4.68629 14 8 14Z"
														stroke="#BCBCBC"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
													<path
														d="M8 12C8.41421 12 8.75 11.6642 8.75 11.25C8.75 10.8358 8.41421 10.5 8 10.5C7.58579 10.5 7.25 10.8358 7.25 11.25C7.25 11.6642 7.58579 12 8 12Z"
														fill="#BCBCBC"
													/>
													<path
														d="M8 9.00049V8.50049C8.34612 8.50049 8.68446 8.39785 8.97225 8.20556C9.26003 8.01327 9.48434 7.73996 9.61679 7.42019C9.74924 7.10042 9.7839 6.74855 9.71637 6.40908C9.64885 6.06961 9.48218 5.75779 9.23744 5.51305C8.9927 5.26831 8.68088 5.10164 8.34141 5.03411C8.00194 4.96659 7.65007 5.00125 7.3303 5.1337C7.01053 5.26615 6.73722 5.49046 6.54493 5.77824C6.35264 6.06603 6.25 6.40437 6.25 6.75049"
														stroke="#BCBCBC"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
												</svg>
											</div>
										</template>
										<span class="tooltipFont"
											>{{ $t("autotradePage.ifNotPay") }}
										</span>
									</v-tooltip>
								</v-card-title>
								<h2 class="white--text t-font-bold t-text-2xl text-center">
									${{ overview.package_detail.borrow_amount }}
								</h2>
								<v-card-text
									class="text-center d-flex flex-column align-center pa-0"
								>
									<v-dialog
										v-if="overview.package_detail.borrow_amount"
										v-model="payDialog"
										width="288"
										persistent
									>
										<template v-slot:activator="{ on, attrs }">
											<v-btn
												width="120px"
												height="38px"
												dark
												v-bind="attrs"
												v-on="on"
												depressed
												color="#03A593"
											>
												<span class="t-capitalize white--text">{{
													$t("autotradePage.pay")
												}}</span>
											</v-btn>
										</template>

										<v-card color="#19232A">
											<v-card-text class="pa-5">
												<label class="grey--text t-mb-3">
													{{ $t("autotradePage.availableBalance") }}
												</label>
												<h2
													class="
                            white--text
                            t-font-bold t-uppercase t-text-2xl t-pb-6
                          "
												>
													${{
														$auth.user.autotrade_balance | displayCurrency(2)
													}}
												</h2>

												<v-text-field
													v-model="amountPay"
													solo
													hide-details
													class="my-5"
													placeholder="Amount"
												/>
											</v-card-text>

											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#515252"
													@click="payDialog = false"
												>
													<span
														class="t-capitalize t-text-xl t-text-gray-300"
														>{{ $t("autotradePage.cancel") }}</span
													>
												</v-btn>

												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#03A593"
													@click="
														Pay({
															amount: amountPay,
															callback: () => {
																payDialog = false;
																amountPay = 0;
															},
														})
													"
												>
													<span class="t-capitalize t-text-xl">{{
														$t("autotradePage.pay")
													}}</span>
												</v-btn>
												<v-spacer></v-spacer>
											</v-card-actions>
										</v-card>
									</v-dialog>

									<v-dialog
										v-else
										v-model="borrowDialog"
										width="400"
										persistent
									>
										<template v-slot:activator="{ on, attrs }">
											<v-btn
												width="120px"
												height="38px"
												dark
												v-bind="attrs"
												v-on="on"
												depressed
												color="#03A593"
											>
												<span class="t-capitalize">{{
													$t("autotradePage.borrow")
												}}</span>
											</v-btn>
										</template>

										<v-card max-width="400" color="#19232A">
											<v-card-text>
												<ul
													class="
                            t-list-disc
                            xl:t-text-base
                            t-text-sm t-leading-4 t-text-gray-300
                          "
												>
													<li class="t-py-2">
														{{ $t("autotradePage.youWillReceive") }}
														<span class="white--text">$300</span>
														{{ $t("autotradePage.mustPay") }}
													</li>
													<li class="t-py-2">
														{{ $t("autotradePage.ifNotTheFund") }}
														<span class="white--text">{{
															$t("autotradePage.autoCancel")
														}}</span>
														{{ $t("autotradePage.allProfit") }}
													</li>
												</ul>
											</v-card-text>

											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#515252"
													@click="borrowDialog = false"
												>
													<span class="t-capitalize t-text-gray-300">{{
														$t("autotradePage.cancel")
													}}</span>
												</v-btn>

												<v-btn
													width="120px"
													height="38px"
													dark
													depressed
													color="#03A593"
													@click="
														Borrow({
															callback: () => {
																borrowDialog = false;
															},
														})
													"
												>
													<span class="t-capitalize">{{
														$t("autotradePage.borrow")
													}}</span>
												</v-btn>
												<v-spacer></v-spacer>
											</v-card-actions>
										</v-card>
									</v-dialog>
									<small class="white--text" v-if="countdown != null">{{
										countdown
									}}</small>
									<small class="white--text" v-else>&nbsp;</small>
								</v-card-text>
							</v-card>
						</v-col>

						<v-col cols="12" md="12" lg="6" xl="6" align="right">
							<v-card
								color="#19232A"
								class="
                  t-p-5
                  t-grid
                  t-grid-flow-row
                  t-auto-rows-max
                  t-gap-1
                  t-border
               t-border-[#03A593]
                  t-rounded-md
                  my-5
                "
							>
								<v-card-title class="pa-0">
									<v-spacer />
									<span
										class="grey--text t-font-bold t-capitalize t-text-sm pl-5"
									>
										{{ $t("autotradePage.activeAuto") }}
									</span>
									<v-spacer />
									<v-tooltip
										right
										color="#19232A"
										max-width="158px"
										class="pointer"
									>
										<template v-slot:activator="{ on, attrs }">
											<div v-bind="attrs" v-on="on">
												<svg
													width="16"
													height="16"
													viewBox="0 0 16 16"
													fill="none"
													xmlns="http://www.w3.org/2000/svg"
												>
													<path
														d="M8 14C11.3137 14 14 11.3137 14 8C14 4.68629 11.3137 2 8 2C4.68629 2 2 4.68629 2 8C2 11.3137 4.68629 14 8 14Z"
														stroke="#BCBCBC"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
													<path
														d="M8 12C8.41421 12 8.75 11.6642 8.75 11.25C8.75 10.8358 8.41421 10.5 8 10.5C7.58579 10.5 7.25 10.8358 7.25 11.25C7.25 11.6642 7.58579 12 8 12Z"
														fill="#BCBCBC"
													/>
													<path
														d="M8 9.00049V8.50049C8.34612 8.50049 8.68446 8.39785 8.97225 8.20556C9.26003 8.01327 9.48434 7.73996 9.61679 7.42019C9.74924 7.10042 9.7839 6.74855 9.71637 6.40908C9.64885 6.06961 9.48218 5.75779 9.23744 5.51305C8.9927 5.26831 8.68088 5.10164 8.34141 5.03411C8.00194 4.96659 7.65007 5.00125 7.3303 5.1337C7.01053 5.26615 6.73722 5.49046 6.54493 5.77824C6.35264 6.06603 6.25 6.40437 6.25 6.75049"
														stroke="#BCBCBC"
														stroke-linecap="round"
														stroke-linejoin="round"
													/>
												</svg>
											</div>
										</template>
										<span class="tooltipFont">
											{{ $t("autotradePage.activateEveryDay") }}
										</span>
									</v-tooltip>
								</v-card-title>
								<div class="no-gap text-center">
									<h2 class="white--text t-font-bold t-text-2xl text-center">
										{{ overview.package_detail.active_counter }}
									</h2>
									<small class="t-text-sm grey--text t-font-bold">{{
										$t("autotradePage.days")
									}}</small>
								</div>
								<v-card-text
									class="text-center d-flex flex-column align-center pa-0"
								>
									<v-btn
										:disabled="countdownActive == null"
										width="120px"
										height="38px"
										dark
										depressed
										color="transparent"
										@click="ActiveBot()"
									>
										<span class="t-capitalize white--text t-text-lg">
											{{ $t("autotradePage.start") }}
										</span>
									</v-btn>
									<small class="white--text" v-if="countdownActive != null">{{
										countdownActive
									}}</small>
									<small class="white--text" v-else>&nbsp;</small>
								</v-card-text>
							</v-card>
						</v-col>
					</v-row>
				</v-col>

				<v-col cols="12" md="5" lg="5" xl="3">
					<v-card
						color="#19232A"
						class="
              t-p-5
              t-grid
              t-grid-flow-row
              t-auto-rows-max
              t-gap-4
              t-border
            t-border-[#03A593]
              t-rounded-md
              t-max-h-full
            "
					>
						<v-form v-if="overview.autotrade_commission != undefined">
							<v-card-title class="d-flex flex-column align-center py-0">
								<span class="grey--text t-font-bold t-capitalize t-text-sm">
									{{ $t("autotradePage.commissionMonth") }}
								</span>
								<h2
									class="white--text t-font-bold t-uppercase t-text-2xl t-pb-6"
								>
									${{
										overview.autotrade_commission.month | displayCurrency(2)
									}}
								</h2>
							</v-card-title>

							<div class="d-flex flex-row justify-space-between">
								<div class="text-center">
									<span class="grey--text t-font-bold t-capitalize t-text-sm">
										{{ $t("autotradePage.active") }}
									</span>
									<h2
										class="white--text t-font-bold t-uppercase t-text-lg t-pb-6"
									>
										{{ overview.autotrade_commission.active }}
									</h2>
								</div>

								<div class="text-center">
									<span class="grey--text t-font-bold t-capitalize t-text-sm">
										{{ $t("autotradePage.notActive") }}
									</span>
									<h2
										class="white--text t-font-bold t-uppercase t-text-lg t-pb-6"
									>
										{{ overview.autotrade_commission.not_active }}
									</h2>
								</div>
							</div>

							<v-spacer></v-spacer>

							<label class="grey--text t-mb-3">{{
								$t("autotradePage.ref")
							}}</label>
							<v-text-field solo hide-details :value="ref_url">
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
									</v-btn>
								</template>
							</v-text-field>
						</v-form>
					</v-card>
				</v-col>
			</v-row>
		</div>
	</div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import moment from "moment";

export default {
	data() {
		return {
			payDialog: false,
			borrowDialog: false,
			withdrawDialog: false,
			swapDialog: false,
			amountSwap: 0,
			amountPay: 0,
			countdown: null,
			countdownActive: null,
			amountWithdraw: 0,
		};
	},
	methods: {
		...mapActions("autotrade", [
			"Overview",
			"BUY_PACKAGE",
			"Swap",
			"Borrow",
			"ActiveBot",
			"Pay",
			"GET",
		]),
		countDown() {
			let self = this;
			setInterval(() => {
				if (self.borrow_overtime != null) {
					const end = moment.utc(
						self.borrow_overtime + " UTC",
						"YYYY-MM-DD HH:mm:ss Z",
					);
					const now = moment.utc();
					let temp = moment.duration(end.diff(now));
					self.countdown =
						temp.get("hours") +
						":" +
						temp.get("minutes") +
						":" +
						temp.get("seconds");
				}
			}, 1000);
		},
	},
	computed: {
		...mapState("autotrade", ["overview"]),
		ref_url() {
			return window.location.origin + "/register?ref=" + this.$auth.user.ref_id;
		},
		borrow_date() {
			return this.overview.package_detail.borrow_date;
		},
		borrow_overtime() {
			if (this.overview.package_detail != undefined) {
				return this.overview.package_detail.borrow_overtime;
			} else {
				return null;
			}
		},
	},
	watch: {
		borrow_overtime() {
			this.countDown();
		},
	},
	mounted() {
		this.Overview();
		let self = this;
		setInterval(() => {
			let timeNow = moment();
			let hourNow = Number(moment().format("H"));
			let tempTime = moment().format("YYYY-MM-DD");
			const leftTime = moment(tempTime + " 12:00:00", "YYYY-MM-DD HH:mm:ss");
			if (hourNow >= 7 || hourNow <= 12) {
				let tempCountdown = moment.duration(leftTime.diff(timeNow));
				self.countdownActive =
					tempCountdown.get("hours") +
					":" +
					tempCountdown.get("minutes") +
					":" +
					tempCountdown.get("seconds");
			} else {
				self.countdownActive = null;
			}
		}, 1000);
	},
};
</script>

<style lang="scss" scoped>
.tooltipFont {
	color: #bcbcbc !important;
	font-size: 0.625rem !important;
}

row-maxWidth {
	max-width: 57.813 !important;
}

.v-tooltip__content {
	background-color: #19232a !important;
	color: #bcbcbc !important;
	line-height: 12px !important;
	letter-spacing: 0.03em !important;
	text-align: left !important;
}
</style>
