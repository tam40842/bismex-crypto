<template>
	<v-app-bar
		height="64"
		absolute
		color="#19232A"
		elevation="0"
		class="t-items-baseline"
	>
		<v-app-bar-nav-icon class="xl:t-hidden z-20">
			<v-btn
				height="50"
				width="50"
				color="#144874"
				elevation="0"
				small
				@click="open"
			>
				<!-- <v-img :src="$svg.menu" contain></v-img> -->
				<v-icon color="white">mdi-menu</v-icon>
			</v-btn>
		</v-app-bar-nav-icon>
		<v-toolbar-title class="md:t-inline-block t-hidden">
			<img :src="$image.logo_mobile2" height="40" width="40" alt="Bitsmex" />
			<!-- <v-img   contain></v-img> -->
		</v-toolbar-title>
		<v-spacer></v-spacer>
		<div
			class="
			t-flex
        md:t-gap-4 md:t-justify-end
        t-justify-between t-gap-2
		board-header
      "
		>
			<v-select
				v-model="$i18n.locale"
				:items="countries"
				item-text="name"
				solo
				background-color="#4F4F5028"
				color="white"
				hide-details
				append-icon=""
				:menu-props="{'z-index':10}"
			>
				<template v-slot:selection="{ item }">
					<v-img max-width="34" max-height="34" :src="item.flag" />
					<div class="t-text-white t-ml-4 t-hidden md:t-block">
						{{ item.name }}
					</div>
				</template>
				<template v-slot:item="slotProps">
					<v-img
						max-width="34"
						max-height="34"
						:src="slotProps.item.flag"
						:class="['mr-2', 'em']"
					/>
					<div class="t-hidden md:t-block">
						{{ slotProps.item.name }}
					</div>
				</template>
			</v-select>
			<v-btn
				icon
				@click="
					$store.commit('settings/SET_VOLUME', !$store.state.settings.volume)
				"
			>
				<v-icon
					color="white"
					v-text="
						$store.state.settings.volume
							? 'mdi-volume-high'
							: ' mdi-volume-mute'
					"
				></v-icon>
			</v-btn>
			<!-- <v-btn color="#1C4F7C" dark height="50" width="150">
				<div class="t-flex t-items-center t-text-left">
					<div class="t-flex t-flex-col 2xl:t-text-base t-text-xs">
						<small class="t-capitalize">Live balance</small>
						<span>${{ $auth.user.live_balance | displayCurrency(2) }}</span>
					</div>
				</div>
			</v-btn>-->

			<v-menu offset-y transition="slide-y-transition" min-width="150">
				<template v-slot:activator="{ on, attrs }">
					<v-btn
						color="#1C4F7C"
						dark
						v-bind="attrs"
						v-on="on"
						height="50"
						width="150"
						class="board-balance"
						:loading="$load('playmode')"
					>
						<div class="t-flex t-items-center t-text-left">
							<div class="t-flex t-flex-col 2xl:t-text-base t-text-xs">
								<small
									class="t-capitalize"
									v-text="$auth.user.play_mode + ' balance'"
								></small>
								<span
									>${{
										$auth.user[$auth.user.play_mode + "_balance"]
											| displayCurrency(2)
									}}</span
								>
							</div>
							<v-icon>mdi-chevron-down</v-icon>
						</div>
					</v-btn>
				</template>
				<v-list color="#1C4F7C">
					<v-list-item-group color="white">
						<v-list-item @click="mode = 'demo'">
							<v-list-item-content>
								<div class="t-flex t-flex-col t-text-left white--text">
									<small>{{ $t("headerBoardPage.demoBalance") }}</small>
                  <div class="t-flex t-items-center t-justify-between">
                    	<span class="t-mt-1">${{ $auth.user.demo_balance | displayCurrency(2) }}</span>
                      <v-btn icon color="green" @click.prevent="_GetDemoBalance">
                          <v-icon>mdi-reload</v-icon>
                      </v-btn>
                  </div>
								</div>
							</v-list-item-content>
						</v-list-item>
						<v-list-item @click="mode = 'live'">
							<v-list-item-content>
								<div class="t-flex t-flex-col t-text-left white--text">
									<small>{{ $t("headerBoardPage.liveBalance") }}</small>
									<span class="t-mt-1 notranslate"
										>${{ $auth.user.live_balance | displayCurrency(2) }}</span
									>
								</div>
							</v-list-item-content>
						</v-list-item>
					</v-list-item-group>
				</v-list>
			</v-menu>
			<v-btn color="#03A593" height="50" small to="/wallet/deposit">
				<!-- <img src="@asset/icon/coin.svg" /> -->
				<v-icon color="white">mdi-wallet</v-icon>
				<!-- <v-icon color="white">mdi-wallet</v-icon> -->
				<span
					class="t-font-bold white--text t-ml-2 2xl:t-inline-block t-hidden"
					>{{ $t("headerBoardPage.deposit") }}</span
				>
			</v-btn>

			<!-- <v-btn

				text
				width="40"
				height="50"
				x-small
				class="t-px-0 md:t-hidden"
				to="/trading"
			>
				<img :src="$image.logo_mobile" height="40" width="40" alt="Bitsmex" />
				<v-img :src="$image.logo_mobile" height="30" contain alt="Bitsmex"></v-img>
			</v-btn> -->
		</div>
	</v-app-bar>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
	data() {
		return {
			languages: [
				{
					code: "en",
					name: "English",
					cname: "英语",
					ename: "English",
				},
				{
					code: "vi",
					name: "Vietnamese",
					cname: "越南语",
					ename: "Vietnamese",
				},
				{
					code: "ja",
					name: "にほんご",
					cname: "日语",
					ename: "Japanese",
				},
				{
					code: "ko",
					name: "한국어",
					cname: "韩语",
					ename: "Korean",
				},
			],
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
	methods: {
    ...mapActions("wallet", ["GET_DEMO_BALANCE"]),
		open(v) {
			this.$emit("open", v);
		},
    _GetDemoBalance() {
      event.preventDefault();
      event.stopPropagation();
      this.GET_DEMO_BALANCE();
      return false;
    },
	},
	computed: {
		mode: {
			get() {
				return this.$store.state.trading.mode;
			},
			set(v) {
				this.$store.dispatch("trading/CHANGE_MODE", v, { root: true });
			},
		},
	},
	async mounted() {
		await this.$store.dispatch("trading/CHANGE_MODE", "live", {
			root: true,
		});
		this.mode = this.$auth.user.play_mode;
	},
};
</script>

<style lang="scss">
.eo__languages {
	.eo__dropdown__activator {
		color: white;

		svg {
			color: white;
			fill: white;
		}
	}

	.language__flag--vi {
		width: 35px;
		height: 35px;
		background-image: url("~/assets/flags/vietnam.png") !important;
		background-size: 35px 35px;
	}
}

.eo__languages {
	.eo__dropdown__menu {
		background-color: #19232a;
		color: white;
	}
}
</style>
