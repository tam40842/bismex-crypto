<template>
	<v-app id="auth" class="t-relative auth-select">
		<div
			class=" t-hidden sm:t-block t-absolute select"
			style="bottom: 0%; left:50%; transform: translateX(-50%); width: 150px; height: 100px;"
		>
			<v-select
				v-model="$i18n.locale"
				:items="countries"
				item-text="name"
				aria-selected=""
				style="color"
			>
				<template v-slot:selection="{ item }">
					<v-img max-width="20" max-height="20" :src="item.flag" />
					<div class="t-text-white t-ml-4">
						{{ item.name }}
					</div>
				</template>
				<template v-slot:item="slotProps">
					<v-img
						max-width="20"
						max-height="20"
						:src="slotProps.item.flag"
						:class="['mr-2', 'em']"
					/>
					<div class="">
						{{ slotProps.item.name }}
					</div>
				</template>
			</v-select>
		</div>
		<v-container
			class="t-min-h-screen t-flex t-flex-col t-justify-center xl:t-w-[40%] lg:t-w-3/5"
		>
			<v-card color="transparent" elevation="0">
				<v-img
					:src="$image.logo_mobile"
					@click="$router.push('/')"
					class="t-mx-auto t-cursor-pointer"
					width="320"
				></v-img>
				<Nuxt />
			</v-card>
		</v-container>
	</v-app>
</template>

<script>
export default {
	auth: false,
	data() {
		return {
			countries: [
				{
					name: "English",
					flag: require("@/assets/images/global/usa.png"),
				},
				{
					name: "Vietnamese",
					flag: require("@/assets/images/global/vi.png"),
				},
				{
					name: "Japanese",
					flag: require("@/assets/images/global/japan.png"),
				},
				{
					name: "Korean",
					flag: require("@/assets/images/global/south-korea.png"),
				},
				{
					name: "Chinese",
					flag: require("@/assets/images/global/china.png"),
				},
			],
		};
	},
	mounted() {
		this.$i18n.locale = localStorage.getItem("language") || "English";
	},
	// computed: {
	// 	items() {
	// 		return [
	// 			{ value: "English", icon: "mdi-message" },
	// 			{ value: "Vietnamese", icon: "mdi-message" },
	// 		]
	// 	},
	// },
};
</script>

<style lang="scss" scoped>
#auth {
	// background-image: url("~/assets/images/global/board_bg.png");
	background: linear-gradient(to bottom, #032e3d, #19232a);
	background-position: center;
	background-repeat: no-repeat;
	background-size: cover;
}
</style>
