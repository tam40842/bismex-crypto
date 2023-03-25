<template>
	<div class="t-w-full t-h-full t-flex" ref="chart">
		<div class="t-flex-grow">
			<client-only>
				<v-chart
					:options="option"
					ref="bar"
					theme="ovilia-green"
					autoresize
					class="echart t-max-h-full t-h-full t-w-full"
					id="echart"
					:style="{ height: setHeight } + 'px;'"
				/>
			</client-only>
		</div>
		<v-dialog :value="!socket_connect" persistent width="350" hide-overlay>
			<v-card color="#FFFFFF50">
				<v-card-text class="t-text-center t-pt-5">
					<!-- <v-icon size="150" color="yellow darken-1">mdi-lan-disconnect</v-icon> -->
					<h2 class="yellow--text darken-1 t-text-lg t-font-bold">
						Has an error about your connection.
						<br />press OK to reconnect
					</h2>
				</v-card-text>
				<v-card-actions class="t-block t-text-center">
					<v-btn @click="loadOnce()" color="white yellow darken-2" width="150"
						>Ok</v-btn
					>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
import ECharts from "vue-echarts";
import "echarts/lib/chart/candlestick";
import "echarts/lib/chart/line";
import "echarts/lib/chart/bar";
// import "echarts/lib/chart/pie";
import "echarts/lib/chart/map";
// import "echarts/lib/chart/radar";
// import "echarts/lib/chart/scatter";
// import "echarts/lib/chart/effectScatter";
// import "echarts/lib/chart/helper/LineDraw";
import "echarts/lib/component/tooltip";
import "echarts/lib/component/markLine";
import "echarts/lib/component/markPoint";
import "echarts/lib/component/polar";
import "echarts/lib/component/geo";
import "echarts/lib/component/legend";
import "echarts/lib/component/title";
// import "echarts/lib/component/visualMap";
// import "echarts/lib/component/dataset";
// import "echarts/lib/util/number";
// import "zrender/lib/svg/svg";
// import "zrender/lib/core/util";

import moment from "moment";
export default {
	layout: "blank",
	name: "Echarts",
	components: {
		"v-chart": ECharts,
	},
	data() {
		let self = this;
		return {
			socket_connect: true,
			option: {
				backgroundColor: "transparent",
				tooltip: {
					trigger: "axis",
					axisPointer: {
						animation: true,
						type: "cross",
						lineStyle: {
							color: "#03A593",
							width: 2,
							opacity: 1,
						},
            label:{
              color: "black"
            }
					},
					position: function(pos, params, el, elRect, size) {
						var obj = { top: 10 };
						obj[["left", "right"][+(pos[0] < size.viewSize[0] / 2)]] = 30;
						return obj;
					},
				},
				grid: [
					{
						top: "2%",
						left: "2%",
						right: "55px",
						height: "67%",
					},
					{
						left: "2%",
						right: "55px",
						top: "73%",
						height: "15%",
					},
				],
				xAxis: [
					{
						type: "category",
						data: [],
						scale: true,
						// boundaryGap: false,
						axisLine: {
							show: false,
							lineStyle: { color: "#03A593", type: "dotted" },
						},
						axisTick: {
							alignWithLabel: false,
							show: false,
						},
						splitLine: {
							show: true,
							lineStyle: { color: "transparent", type: "dotted" },
						},
						splitNumber: 20,
						axisPointer: {
							z: 100,
						},
						axisLabel: { show: false },
					},
					{
						type: "category",
						gridIndex: 1,
						data: [],
						axisLabel: {
							fontSize: 12,
							color: "#8392a5",
							show: true,
							formatter: function(value, index) {
								if (index % 2 == 0) {
									return Number(value.split(":")[0]);
								} else {
									return "";
								}
							},
						},
					},
				],
				yAxis: [
					{
						scale: true,
						position: "right",
						axisLine: { lineStyle: { color: "white" } },
						splitLine: {
							show: false,
							lineStyle: { color: "white", type: "dotted" },
						},
					},
					{
						gridIndex: 1,
						splitNumber: 4,
						max: 30,
						min: 0,
						position: "right",
						axisLabel: {
							show: true,
							color: "white",
							fontSize: 10,
						},
						axisLine: {
							show: true,
							lineStyle: { color: "white" },
						},
						axisTick: { show: false },
						splitLine: { show: false },
					},
				],
				animation: true,
				series: [
					{
						name: "Bitsmex",
						type: "candlestick",
						data: [],
						itemStyle: {
							color: "#03A593",
							color0: "#FD1050",
							borderColor: "#03A593",
							borderColor0: "#FD1050",
						},
						markLine: {
							data: [
								{
									yAxis: 0,
									symbol: "none",
								},
							],
							symbolSize: 0,
							label: {
								backgroundColor: "#fff",
								color: "#fff",
								padding: 5,
								formatter: function(data, index) {
									return Math.floor(data.value);
								},
							},
							lineStyle: {
								color: "#fff",
							},
						},
						markPoint: {
							symbol:
								"path://M320 284.72L340.4 266.62L320 247.72L327.65 264.9L309 264.9L309 268.77L327.65 268.77L320 284.72Z",
							symbolSize: 15,
							symbolOffset: [-8, 0],
							itemStyle: {
								color: "#03A593",
							},
							data: [
								{
									name: "highest value",
									type: "max",
									valueDim: "highest",
									label: {
										color: "#fff",
										offset: [-40, 0],
										emphasis: {
											offset: [0, 40],
										},
										formatter: (t) => t.value.toLocaleString("en"),
									},
								},

								{
									name: "lowest value",
									type: "min",
									valueDim: "lowest",
									label: {
										color: "#CF304A",
										offset: [-40, 0],
										emphasis: {
											offset: [0, 40],
										},
										formatter: (t) => t.value.toLocaleString("en"),
									},
								},
							],
						},
						barWidth: "60%",
					},
					{
						name: "MA6",
						type: "line",
						data: [],
						smooth: true,
						showSymbol: false,
						lineStyle: {
							width: 1,
							color: "#03A593",
						},
					},
					{
						name: "MA13",
						type: "line",
						data: [],
						smooth: true,
						showSymbol: false,
						lineStyle: {
							width: 1,
							color: "#CF304A",
						},
					},
					{
						name: "Volume",
						type: "bar",
						xAxisIndex: 1,
						yAxisIndex: 1,
						data: [],
						itemStyle: {
							color: function(param, data) {
								let color =
									self.option.series[0].data[param.data[0]][1] >=
									self.option.series[0].data[param.data[0]][0]
										? "#03A593"
										: "#CF304A";
								let time = self.option.xAxis[1].data[param.data[0]].split(
									":",
								)[1];
								if (time != 30) {
									color = "#4f4f5087";
								}
								return color;
							},
						},
						barWidth: "60%",
					},
				],
			},
			roomstatus: {
				buy: 50,
				sell: 50,
			},
			size: { width: window.innerWidth, height: window.innerHeight },
			dataChart: [],
			dataMa: [],
		};
	},
	computed: {
		numberOfCandle() {
			if (this.$vuetify.breakpoint.mobile) {
				return 26;
			} else {
				return 60;
			}
		},
	},
	sockets: {
		connect() {
			console.log("Socket connected");
			this.sockets = true;
		},
		join(data) {
			this.dataChart = data.candles;
			this.drawChart(data.candles);
		},
		candles(p) {
			var oldV = 0;
			if (this.option) {
				if (
					this.option.xAxis[0].data.includes(moment(p.t * 1000).format("mm:ss"))
				) {
					this.dataChart.splice([this.dataChart.length - 1], 1, p);
				} else {
					this.dataChart.push(p);
				}

				this.drawChart(this.dataChart);
			}
		},
		disconnect() {
			this.socket_connect = false;
		},
	},
	methods: {
		drawChart(data) {
			let self = this;
			self.dataMa = data.map((p) => [
				Number(p.o),
				Number(p.c),
				Number(p.l),
				Number(p.h),
			]);
			data = _.takeRight(data, self.numberOfCandle);

			let volume_id = 0;
			this.option.series[3].data = data.map((p) => {
				return [volume_id++, p.v, p.v];
			});
			this.option.series[0].data = data.map((p) => [
				Number(p.o),
				Number(p.c),
				Number(p.l),
				Number(p.h),
			]);
			this.option.series[1].data = this.calculateMA(6, self.dataMa);
			this.option.series[2].data = this.calculateMA(13, self.dataMa);
			this.option.series[0].markLine.data[0].yAxis = Number(
				this.option.series[0].data[this.option.series[0].data.length - 1][1],
			);
			let color =
				this.option.series[0].data[this.option.series[0].data.length - 1][1] >=
				this.option.series[0].data[this.option.series[0].data.length - 1][0]
					? "#03A593"
					: "#CF304A";
			this.option.series[0].markLine.label.backgroundColor = color;
			this.option.series[0].markLine.lineStyle.color = color;
			this.option.series[0].markLine.lineStyle.width = 0.8;

			this.option.xAxis[0].data = data.map((p) =>
				moment(p.t * 1000).format("mm:ss"),
			);
			this.option.xAxis[1].data = data.map((p) =>
				moment(p.t * 1000).format("mm:ss"),
			);
		},
		calculateMA(dayCount, data) {
			var result = [];
			for (var i = 0, len = data.length; i < len; i++) {
				if (i < dayCount) {
					result.push("-");
					continue;
				}
				var sum = 0;
				for (var j = 0; j < dayCount; j++) {
					sum += data[i - j][1];
				}
				result.push(sum / dayCount);
			}

			return _.takeRight(result, this.numberOfCandle);
		},
		onResize() {
			this.width = this.$refs.chart.clientWidth;
			this.height = this.$refs.chart.clientHeight;
		},
		loadOnce() {
			location.reload();
		},
	},
	mounted() {
		window.addEventListener("resize", this.onResize);
		this.$socket.emit("id", this.$auth.user.id);
		this.$socket.emit("join", this.market);
	},
	props: {
		market: {
			type: String,
			required: true,
			default: null,
		},
		colorBack: {
			type: String,
			default: "#ffffff01",
		},
		colorGrid: {
			type: String,
			default: "#ffffff01",
		},
		colorText: {
			type: String,
			default: "#fff",
		},
		candle_up: {
			type: String,
			default: "#03A593",
		},
		candle_dw: {
			type: String,
			default: "#CF304A",
		},
		wick_dw: {
			type: String,
			default: "#CF304A",
		},
		wick_up: {
			type: String,
			default: "#03A593",
		},
		colorVolUp: {
			type: String,
			default: "#03A593",
		},
		colorVolDw: {
			type: String,
			default: "#CF304A",
		},
		setHeight: {
			type: Number,
		},
	},
	watch: {
		market: {
			handler: function(newVal, oldVal) {
				//VC -- LOi Ko hien thi oldVal
				if (oldVal == null && newVal !== oldVal) {
					this.$socket.emit("join", newVal);
				}
				if (oldVal !== null && newVal !== oldVal) {
					this.$socket.emit("leave", oldVal);
					this.$socket.emit("join", newVal);
				}
				return newVal;
			},
			deep: true,
		},
	},
	beforeDestroy() {
		let vm = this;
		this.$nextTick(() => {
			window.removeEventListener("resize", vm.onResize);
		});
	},
};
</script>
<style lang="scss">
.echart {
	width: 100%;
	height: 100%;
	//   @media (width: 412px) {
	//     max-height: 350px;
	//   }
}
</style>
