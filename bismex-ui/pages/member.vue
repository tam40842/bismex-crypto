<template>
	<v-container
		fluid
		class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 lg:t-p-5 t-p-3"
		id="network"
	>
		<div
			class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4 lg:t-p-5 t-p-3 t-mx-auto t-w-full"
		>
			<v-card
				color="#19232A"
				class="t-border t-border-[#03A593] t-p-4 t-w-[100%] md:t-w-[40%] "
			>
				<v-card-text class="t-flex t-flex-col">
					<label class="white--text t-mb-3 t-font-bold">{{
						$t("memberPage.registrationLink")
					}}</label>
					<v-text-field
						solo
						hide-details
						:value="ref_url"
						append-icon="mdi-content-copy"
						@click:append="$copy(ref_url)"
						color="#EBA900"
						class="member"
						background-color="transparent"
					>
						<template v-slot:append>
							<v-btn icon color="#EBA900">
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
				</v-card-text>
			</v-card>

			<v-card
				color="#19232A"
				class="t-border t-border-[#03A593] t-p-4 t-max-w-full t-overflow-x-hidden t-flex t-flex-col"
			>
				<v-card-title class="t-py-0">
					<h3 class="mx-auto t-capitalize t-text-custom-FFE600 t-font-bold">
						{{ $t("memberPage.volume") }}
					</h3>
				</v-card-title>
				<ECharts :options="chart" class="lg:t-h-80 t-h-52 t-w-full " />
				<v-card-actions
					class="t-block t-text-center xl:t-text-3xl t-font-bold white--text"
					>{{ $t("memberPage.total") }}: ${{
						total_volume | displayCurrency(2)
					}}</v-card-actions
				>
			</v-card>
			<v-card
				color="#19232A"
				class="t-border t-border-[#03A593] t-max-w-full t-overflow-x-auto"
			>
				<v-card-title class="t-flex t-items-center">
					<div class="t-block white--text">{{ $t("memberPage.tree") }}:</div>
					<v-breadcrumbs :items="arr_breakcrumb">
						<template v-slot:divider>
							<v-icon color="white">mdi-forward</v-icon>
						</template>
						<template v-slot:item="{ item }">
							<v-breadcrumbs-item>
								<v-btn
									:color="item === email ? 'grey' : 'white'"
									text
									@click="
										item === email && arr_breakcrumb.length >= 6
											? null
											: member({ page: 1, email: item })
									"
									class="member-btn"
									>F{{ arr_breakcrumb.indexOf(item) + 1 }}</v-btn
								>
							</v-breadcrumbs-item>
						</template>
					</v-breadcrumbs>
				</v-card-title>
				<v-skeleton-loader
					v-if="$load('member')"
					type="table"
				></v-skeleton-loader>
				<v-simple-table
					class="t-bg-transparent"
					id="member-table"
					fixed-header
					height="500"
					v-if="!$load('member') || (statistics && statistics.length > 0)"
				>
					<template v-slot:default>
						<thead>
							<tr class="t-bg-transparent white--text">
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.id") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.email") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.volumeYesterday") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.volumeToday") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.comYesterday") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.registrationLink") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.comMonth") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.comTotal") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.members") }}
								</th>
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.status") }}
								</th>
								<!-- <th class="t-bg-custom-07131C white--text t-text-center">Action</th> -->
								<th class="t-bg-custom-03A593 white--text t-text-center">
									{{ $t("memberPage.registration") }}
								</th>
							</tr>
						</thead>
						<tbody v-if="statistics.length > 0">
							<tr
								v-for="(v, i) in statistics"
								:key="i"
								class="hover:t-bg-custom-03A593 hover:t-bg-opacity-10 t-transition-all t-duration-300 white--text"
								:class="v.total_f1 ? 't-cursor-pointer' : ''"
								@click="
									v.total_f1 > 0 ? member({ page: 1, email: v.email }) : null
								"
							>
								<td class="t-text-center">{{ i + 1 }}</td>
								<td class>{{ v.email }}</td>
								<td class="t-text-center">
									${{ v.volume_subday | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ v.volume_today | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ v.commission_subday | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ v.commission_today | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ v.commission_month | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									${{ v.commission_total | displayCurrency(2) }}
								</td>
								<td class="t-text-center">
									{{ v.total_f1 ? v.total_f1 : 0 }} [{{
										v.total_user_f1_active ? v.total_user_f1_active : 0
									}}]
								</td>
								<td
									class="t-text-center t-inline-grid t-gap-1 t-auto-cols-fr t-grid-flow-col"
								>
									<v-icon color="#03A593" size="16" v-if="v.user_status"
										>mdi-check-circle</v-icon
									>
									<v-icon color="#cf3149" size="16" v-else
										>mdi-alert-circle</v-icon
									>
									<v-icon color="#03A593" size="16" v-if="v.active == 1"
										>mdi-diamond-stone</v-icon
									>
								</td>
								<!-- <td class="t-text-center">
									<v-btn icon tab x-small>
										<v-icon color="#CF304A" size="16">mdi-minus-circle</v-icon>
									</v-btn>
								</td>-->
								<td class="t-text-center">
									{{ v.registration | displayDate("YYYY/MM/DD") }}
								</td>
							</tr>
						</tbody>
					</template>
				</v-simple-table>
				<v-card-actions v-if="statistics && statistics.last_page > 1">
					<v-pagination
						:value="statistics.current_page"
						:length="statistics.last_page"
						@next="
							member({
								page: statistics.current_page + 1,
								email: email || $auth.user.email,
							})
						"
						@previous="
							member({
								page: statistics.current_page - 1,
								email: email || $auth.user.email,
							})
						"
						@input="
							(v) => member({ page: v, email: email || $auth.user.email })
						"
						total-visible="7"
						color="#EBA900"
					></v-pagination>
				</v-card-actions>
			</v-card>
			<v-card color="#19232A" class="t-border t-border-custom-03A593 t-p-4">
				<v-card-actions
					class="t-px-4 t-border-t t-border-custom-EFEFEF py-3 t-border-opacity-20 t-bg-[#00695D]"
				>
					<div
						class="t-grid t-grid-cols-2 2xl:t-gap-8 t-gap-4 t-ml-auto t-mr-0"
					>
						<div
							class="t-grid xl:t-grid-flow-col t-grid-flow-row t-gap-2 t-items-center"
						>
							<label for="from" class="white--text">{{
								$t("memberPage.date")
							}}</label>
							<v-menu
								ref="menu"
								v-model="date_popup_to"
								:close-on-content-click="false"
								:return-value.sync="date_to"
								transition="scale-transition"
								offset-y
								min-width="auto"
							>
								<template v-slot:activator="{ on }">
									<v-text-field
										id="from"
										background-color="#FFF"
										solo
										hide-details
										:label="$t('memberPage.toTime')"
										v-model="date_to"
										readonly
										v-on="on"
										class="t-cursor-pointer"
									>
										<template #append>
											<v-icon v-on="on">mdi-calendar</v-icon>
										</template>
									</v-text-field>
								</template>
								<v-date-picker
									v-model="date_to"
									no-title
									scrollable
									color="#EBA900"
									elevation="0"
									type="month"
								>
									<v-spacer></v-spacer>
									<v-btn text color="primary" @click="date_popup_to = false"
										>Cancel</v-btn
									>
									<v-btn text color="primary" @click="$refs.menu.save(date_to)"
										>OK</v-btn
									>
								</v-date-picker>
							</v-menu>
						</div>
						<div class="t-flex t-items-end t-pl-3">
							<v-btn
								color="#03A593"
								class="t-w-full"
								:height="50"
								@click="total_volume_f1()"
								elevation="0"
								>{{ $t("memberPage.search") }}</v-btn
							>
						</div>
					</div>
				</v-card-actions>
				<v-simple-table
					class="t-bg-transparent"
					id="member-table"
					fixed-header
					height="500"
					v-if="!$load('member') || (statistics && statistics.length > 0)"
				>
					<template v-slot:default>
						<thead class="t-bg-[#03A593]">
							<tr class="white--text">
								<th class="t-bg-[#00695D] white--text t-text-center">
									{{ $t("memberPage.tree") }}
								</th>
								<th class="t-bg-[#00695D] white--text t-text-center">
									{{ $t("memberPage.email") }}
								</th>
								<th class="t-bg-[#00695D] white--text t-text-center">
									{{ $t("memberPage.total") }}
								</th>
								<!-- <th class="t-bg-custom-1E4968 white--text t-text-center">Date</th> -->
							</tr>
						</thead>
						<tbody v-if="volume_f1.length > 0">
							<tr
								v-for="(v, i) in volume_f1"
								:key="i"
								class="hover:t-bg-[#03A593] hover:t-bg-opacity-10 t-transition-all t-duration-300 white--text"
							>
								<td class="t-text-center">F1</td>
								<td class="t-text-center">{{ v.email }}</td>
								<td class="t-text-center">
									${{ v.total_volume | displayCurrency(2) }}
								</td>
								<!-- <td class="t-text-center">{{ "2021/20/09"| displayDate('YYYY/MM/DD HH:mm:ss') }}</td> -->
							</tr>
						</tbody>
					</template>
				</v-simple-table>
			</v-card>
		</div>
	</v-container>
</template>

<script>
import { mapActions, mapState } from "vuex";
import "echarts";
import ECharts from "vue-echarts";
export default {
	layout: "board",
	components: {
		ECharts,
	},
	data() {
		return {
			symbol: null,
			page: 1,
			date_popup_from: false,
			date_popup_to: false,
			date_from: null,
			date_to: null,
			date: null,
			router: [
				{
					name: "Member",
					path: "/member/statistics",
					active: "member-statistics",
				},
				// {
				// 	name: "Level",
				// 	path: "/member/level",
				// 	active: "member-level",
				// },
			],
			ref_url:
				window.location.origin + "/register?ref=" + this.$auth.user.ref_id,
			option: {
				tree: false,
			},
			chart: {
				xAxis: {
					type: "category",
					data: [],
					axisLine: { lineStyle: { color: "white" } },
				},
				yAxis: {
					type: "value",
					axisTick: {
						alignWithLabel: true,
					},
					axisLine: { lineStyle: { color: "#03A593" } },
				},
				series: [
					{
						name: "Member",
						data: [],
						type: "bar",
						itemStyle: {
							color: "#03A593",
						},
					},
				],
				grid: [
					{
						left: "5%",
						right: "0",
						bottom: "5%",
						containLabel: true,
					},
					{
						left: "5%",
						right: "0",
						bottom: "5%",
						containLabel: true,
					},
				],
				tooltip: {
					trigger: "axis",
					// axisPointer: {
					// 	// 坐标轴指示器，坐标轴触发有效
					// 	type: "shadow", // 默认为直线，可选为：'line' | 'shadow'
					// },
				},
			},
			email: null,
			arr_breakcrumb: [],
		};
	},
	methods: {
		...mapActions("commission", ["GET_MEMBER", "POST_TOTAL_VOLUME"]),
		async member(data) {
			this.email = data.email;

			await this.GET_MEMBER({
				...data,
				callback: () => this.breakcrumb(data.email),
			});
			// await this.GET_MEMBER(data);
			this.chart.xAxis.data = this.data_f1_date;
			this.chart.series[0].data = this.data_f1_volume;
		},
		async breakcrumb(data) {
			let index = this.arr_breakcrumb.indexOf(data);
			switch (index) {
				case 0:
					this.arr_breakcrumb = [data];
					break;
				case -1:
					this.arr_breakcrumb.push(data);
					break;
				default:
					this.arr_breakcrumb = this.arr_breakcrumb.slice(0, index + 1);
					break;
			}
		},

		total_volume_f1() {
			this.POST_TOTAL_VOLUME({ month: this.date_to });
		},
	},
	async mounted() {
		await this.member({ page: 1, email: this.$auth.user.email });
		await this.POST_TOTAL_VOLUME();
	},
	computed: {
		...mapState("commission", [
			"data_f1_date",
			"data_f1_volume",
			"total_volume",
			"statistics",
			"volume_f1",
		]),
	},
};
</script>
<style lang="scss" scoped>
#member-table {
	th,
	td {
		font-size: 9px;
		@screen md {
			@apply t-text-base;
		}
	}
}
</style>
