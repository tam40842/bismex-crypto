<template>
	<div class="t-block">
		<v-card color="#19232A" class="t-border t-border-custom-EFEFEF t-p-4">
			<v-card-title
				class="t-block t-text-center"
				style="color: #CCF8FF"
			>Note: The amount you join will be locked for 24 hours</v-card-title>
			<v-card-text>
				<v-list color="transparent" class="xl:t-w-4/5 t-mx-auto">
					<v-list-item v-for="(item, index) in list" :key="index" class="t-py-0">
						<div class="t-block t-w-full">
							<div class="t-flex sm:t-flex-row t-flex-col sm:t-items-center t-mb-3">
								<div class="t-flex-grow sm:pr-3 t-flex t-items-center">
									<div
										class="t-inline-block t-h-3 t-w-3 t-border t-rounded"
										:class="classCheckbox(item.status)"
									></div>
									<strong :class="classText(item.status)" class="t-mx-2">{{item.name}}:</strong>
									<span :class="classDes(item.status)">{{item.des}}</span>
								</div>
								<v-btn
									class="white--text"
									v-text="item.status > 0 ? 'join' : 'joined'"
									small
									width="80"
									:color="item.status === 0 ? '#01C176' : '#2BB99F'"
									:disabled="item.status >= 2"
								></v-btn>
							</div>
							<v-img :src="$image.line_green_full"></v-img>
						</div>
					</v-list-item>
				</v-list>
			</v-card-text>
		</v-card>
		<v-card-title class="t-block t-mt-3 white--text">For example: you join package 1</v-card-title>
		<v-card-subtitle>
			<ul class="t-block mx-auto t-list-disc white--text">
				<li>If your branch has a total profit of -$1.000,00. You will get +$100,00</li>
				<li>If your branch has a total profit of -$1.000,00. You will get +$100,00</li>
			</ul>
		</v-card-subtitle>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				list: [
					{
						status: 0,
						name: "PACK I",
						des: "For $5,000,00 you will get 10%",
					},
					{
						status: 1,
						name: "PACK II",
						des: "For $5,000,00 you will get 10%",
					},
					{
						status: 2,
						name: "PACK III",
						des: "For $5,000,00 you will get 10%",
					},
					{
						status: 2,
						name: "PACK IV",
						des: "For $5,000,00 you will get 10%",
					},
					{
						status: 2,
						name: "PACK V",
						des: "For $5,000,00 you will get 10%",
					},
				],
			};
		},
		methods: {
			classCheckbox(value) {
				let _class = "";
				switch (value) {
					case 0:
						_class = "t-border-white t-bg-custom-01C176";
						break;
					case 1:
						_class = "t-border-white t-bg-custom-EBA900";
						break;

					default:
						_class = "t-border-gray-500";
						break;
				}
				return _class;
			},
			classText(value) {
				let _class = "";
				switch (value) {
					case 0:
						_class = "t-text-custom-01C176";
						break;
					case 1:
						_class = "t-text-custom-EBA900";
						break;

					default:
						_class = "t-text-gray-500";
						break;
				}
				return _class;
			},
			classDes(value) {
				let _class = "";
				switch (value) {
					case 0:
						_class = "t-text-custom-01C176";
						break;
					case 1:
						_class = "t-text-white";
						break;

					default:
						_class = "t-text-gray-500";
						break;
				}
				return _class;
			},
		},
	};
</script>

<style>
</style>