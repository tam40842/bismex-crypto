<template>
  <div
    class="
      t-w-full
      t-p-0
      t-pb-1
      t-max-h-full
      t-h-full
      t-overflow-y-auto
      t-overflow-x-hidden
    "
    :style="
      $vuetify.breakpoint.mobile && $store.state.settings.orientation
        ? 'max-height: calc(100vh - 190px) !important;'
        : ''
    "
  >
    <div class="xl:t-flex t-hidden t-h-full" v-if="!$vuetify.breakpoint.mobile">
      <div class="t-flex-grow t-flex">
        <div class="t-flex-grow t-relative">
          <div class="t-flex-grow t-flex t-flex-col t-h-full t-relative">
            <div :class="'t-h-3/4'">
              <keep-alive>
                <Echart
                  v-if="market_active"
                  :market="market_active.market_name"
                />
              </keep-alive>
              <TradingMarket
                v-if="market_active"
                :list="market_list"
                :active="market_active"
                @update="update"
              />
              <v-btn
                v-if="$vuetify.breakpoint.mobile"
                class="white--text t-rounded t-absolute t-top-3 t-z-10"
                style="left: 110px !important"
                fab
                x-small
                outlined
                color="white"
                @click="toggleBlur()"
              >
                <v-icon v-if="!show_blur">mdi-dots-grid</v-icon>
                <v-icon v-else>mdi-clipboard-text-clock</v-icon>
              </v-btn>
              <v-btn
                v-if="!$vuetify.breakpoint.mobile"
                class="white--text t-rounded t-absolute t-top-3 t-z-10"
                style="left: 110px !important"
                fab
                x-small
                outlined
                color="white"
                @click="toggleShowHistory()"
              >
                <v-icon v-if="!show_history">mdi-clipboard-text-clock</v-icon>
                <v-icon v-else>mdi-close-box</v-icon>
              </v-btn>
            </div>
            <div
              class="t-flex-grow t-flex t-flex-col t-items-center"
              style="justify-content: center; padding-bottom: 50px"
            >
              <v-sheet color="transparent">
                <div class="md:t-block t-hidden">
                  <TradingBlurDesktop :blurData="blur.get_blur_100" />
                </div>
              </v-sheet>
            </div>
          </div>
        </div>
      </div>
      <v-sheet
        color="transparent"
        class="t-px-3 t-pb-3 t-flex t-flex-col t-h-full"
        width="380"
      >
        <div
          class="t-flex-grow t-pb-4"
          v-if="height_pc !== null"
          :style="{ height: `calc(100% - ${height_pc}px)` }"
        >
          <TradingHistoryFull />
        </div>
        <div
          class="t-grid t-grid-flow-row t-auto-rows-max t-gap-4"
          ref="control_pc"
        >
          <div class="t-grid t-grid-flow-row t-auto-rows-max">
            <TradingProfit :value="amount" />
            <TradingCalculator :value="amount" @change="(v) => (amount = v)" />
            <TradingPercent
              :progress="roomstatus"
            />
            <TradingActionsDesktop
              :status="status"
              :timer="time"
              :buy="order.BUY"
              :sell="order.SELL"
              @buy="[openType('BUY'), POST_PLACED('BUY')]"
              @sell="[openType('SELL'), POST_PLACED('SELL')]"
            />
          </div>
        </div>
      </v-sheet>
    </div>
    <div
      :class="$store.state.settings.orientation ? '' : 't-h-full'"
      class="xl:t-hidden t-block"
      v-else
    >
      <div class="t-flex white--text" :class="layout">
        <div
          :class="trading"
          class="t-flex t-h-full"
          :style="
            $vuetify.breakpoint.mobile && $store.state.settings.orientation
              ? 'height: calc(100vh - 430px) !important;'
              : 'height: 100% !important;'
          "
        >
          <div class="t-flex-grow t-relative t-h-full t-w-full">
            <Echart v-if="market_active" :market="market_active.market_name" />
            <TradingMarket
              v-if="market_active"
              :list="market_list"
              :active="market_active"
              @update="update"
            />
            <v-btn
              class="white--text t-rounded t-absolute t-top-3 t-z-10"
              style="left: 110px !important"
              fab
              x-small
              outlined
              color="white"
              @click="toggleBlur()"
            >
              <v-icon v-if="!show_blur">mdi-dots-grid</v-icon>
              <v-icon v-else>mdi-clipboard-text-clock</v-icon>
            </v-btn>
            <!-- <v-btn
                class="white--text t-rounded t-absolute t-top-3 t-z-10"
                style="left: 150px !important"
                fab
                x-small
                outlined
                color="white"
                @click="toggleShowHistory()"
              >
                <v-icon v-if="!show_history">mdi-clipboard-text-clock</v-icon>
                <v-icon v-else>mdi-close-box</v-icon>
              </v-btn> -->
          </div>
          <TradingProgressDesktop
            :progress="roomstatus"
            v-if="!$store.state.settings.orientation"
          />
          <TradingProgressMobile :progress="roomstatus" v-else />
        </div>
        <div
          :class="control"
          class="t-relative t-overflow-y-hidden"
          :style="
            $vuetify.breakpoint.mobile && $store.state.settings.orientation
              ? 'height: 100px !important;'
              : 'height: 100% !important;'
          "
        >
          <div class="t-grid t-grid-flow-row t-auto-rows-max t-gap-1 t-p-1">
            <TradingHistorySort v-if="!show_blur" @open="dialog = true" @openHistory="toggleShowHistory()" />
            <div class="t-grid t-grid-flow" v-else>
              <TradingBlurMobile :blurData="blur.get_blur_60" />
            </div>
          </div>
          <div
            class="t-grid t-grid-flow-row t-auto-rows-max t-gap-1 t-p-1 t-fixed"
            style="bottom: 0px; width: inherit; background-color: transparent"
          >
            <div class="t-w-full">
              <!-- <TradingMobileProfit :value="amount" /> -->
              <TradingCalculator
                :value="amount"
                @change="(v) => (amount = v)"
              />
              <TradingActionsMobile
                :status="status"
                :timer="time"
                :buy="order.BUY"
                :sell="order.SELL"
                @buy="[openType('BUY'), POST_PLACED('BUY')]"
                @sell="[openType('SELL'), POST_PLACED('SELL')]"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <v-dialog v-model="dialog" v-if="dialog">
      <v-card color="#07131C" class="t-border-custom-49D3FF">
        <v-toolbar color="#07131C" elevation="0">
          <v-toolbar-title class="white--text t-text-sm"
            >History</v-toolbar-title
          >
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn fab icon small @click="dialog = false">
              <v-icon size="20" color="white">mdi-close</v-icon>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-divider class="t-border-custom-EFEFEF t-border-opacity-50 t-py-2" />
        <TradingHistoryFullMobile :border="false" />
      </v-card>
    </v-dialog>


    <v-dialog v-model="show_history" persistent max-width="450">
      <v-card color="#07131C" class="t-border-custom-49D3FF">
        <v-toolbar color="#07131C" elevation="0">
          <v-toolbar-title class="white--text t-text-xl"
            >History</v-toolbar-title
          >
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn fab icon small @click="show_history = false">
              <v-icon size="20" color="white">mdi-close</v-icon>
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-divider class="t-border-custom-EFEFEF t-border-opacity-50 t-py-2" />
        <TradingHistoryFullMobileBig :border="false" />
      </v-card>
    </v-dialog>

    <v-dialog :value="open_result" persistent fullscreen>
      <div
        class="
          t-relative t-flex t-flex-col t-items-center t-justify-center t-h-full
        "
        v-if="result !== null"
      >
        <img v-if="result.status === 'WIN'" :src="$svg.win" width="400" />
        <svg
          width="300"
          height="150"
          viewBox="0 0 300 150"
          xmlns="http://www.w3.org/2000/svg"
          class="
            t-absolute
            t-top-1/2
            t-left-1/2
            t-transform
            t--translate-x-1/2
            t--translate-y-3/4
          "
        >
          <text
            x="50%"
            y="50%"
            dominant-baseline="middle"
            text-anchor="middle"
            stroke="white"
            stroke-width=".5"
            :fill="'white'"
            font-size="1em"
          >
            Congratulation!
          </text>
        </svg>
        <svg
          width="300"
          height="150"
          viewBox="0 0 300 150"
          xmlns="http://www.w3.org/2000/svg"
          class="
            t-absolute
            t-top-1/2
            t-left-1/2
            t-transform
            t--translate-x-1/2
            t--translate-y-14
          "
        >
          <text
            x="50%"
            y="40%"
            dominant-baseline="middle"
            text-anchor="middle"
            stroke="#03A593"
            stroke-width=".5"
            :fill="'#03A593'"
            font-size="2em"
          >
            {{ result.status === "WIN" ? "+" : "-" }}
            ${{ result.total | displayCurrency(2) }}
          </text>
        </svg>
      </div>
    </v-dialog>
    <div
      class="t-fixed t-top-0 t-left-0 t-w-full t-h-full"
      v-if="type && amount > 0"
    >
      <div
        class="t-relative t-flex t-flex-col t-items-center t-mt-40 t-h-full"
        :class="
          type === 'BUY' ? 't-text-custom-03A593' : 't-text-custom-CF304A'
        "
      >
        <div class="t-text-4xl t-font-bold">{{ type }}</div>
        <div class="t-text-6xl t-font-bold">
          ${{ Number(amount) | displayCurrency(2) }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Echart from "@/components/Trading/Echart";
import { mapActions, mapMutations, mapState } from "vuex";
import TradingModel from "~/models/trading.model";

export default {
  components: {
    Echart,
  },
  layout: "board",
  name: "trading",
  data() {
    return {
      mobile: false,
      dialog: false,
      time: 0,
      status: true,
      roomstatus: 0,
      result: null,
      open_result: false,
      type: null,
      height_pc: null,
      blur: new TradingModel(),
      show_blur: false,
      show_history: false,
    };
  },
  computed: {
    ...mapState("trading", ["market_list", "market_active", "order","audio"]),
    layout() {
      if (this.$store.state.settings.orientation) {
        return "t-flex-col t-h-auto";
      } else {
        return "t-flex-row t-h-full";
      }
    },
    trading() {
      if (this.$store.state.settings.orientation) {
        return "t-flex-grow t-flex-col";
      } else {
        return "t-w-1/2 t-flex-row";
      }
    },
    control() {
      if (this.$store.state.settings.orientation) {
        return "t-w-full";
      } else {
        return "t-w-1/2";
      }
    },
    amount: {
      get() {
        return this.$store.state.trading.amount;
      },
      set(v) {
        this.$store.commit("trading/AMOUNT", v, { root: true });
      },
    },
  },
  sockets: {
    blurs(e) {
      this.blur.general(e);
    },
    timer(e) {
      this.time = Number(e.seconds);
      this.status = e.status === "open";
      // if (this.time === 0)
      // 	this.$store.commit("trading/AMOUNT", 10, { root: true });
      if (!this.open_result && this.$store.state.settings.volume) {
        if (this.time >= 54 && 59 >= this.time) {
          // this.$refs.audio.src = heartbeat;
          // this.$refs.audio.volume = 0.5;
          // this.$refs.audio.play();
          this.PLAY_AUDIO(this.audio.heartbeat);
        } else if (this.time === 0) {
          // this.$refs.audio.src = start;
          // this.$refs.audio.volume = 0.5;
          // this.$refs.audio.play();
          this.PLAY_AUDIO(this.audio.start);
          this.$store.commit("trading/AMOUNT", 10, { root: true });
        } else {
          // this.$refs.audio.src = bettick;
          // this.$refs.audio.volume = 0.1;
          // this.$refs.audio.play();
          this.PLAY_AUDIO(this.audio.bettick);
        }
      }
    },
    roomstatus(e) {
      this.roomstatus = Number(((e.buy / (e.sell + e.buy)) * 100).toFixed(0));
    },
    async results(e) {
      let result = JSON.parse(e);
      this.result = result.result;
      if (this.result.status === "WIN") {
        this.open_result = true;
      }
      if (this.$store.state.settings.volume) {
        if (this.result.status === "WIN") {
          // this.$refs.audio.src = win;
          // this.$refs.audio.volume = 0.5;
          // this.$refs.audio.play();
          this.PLAY_AUDIO(this.audio.win);
        } else {
          // this.$refs.audio.src = lose;
          // this.$refs.audio.volume = 0.5;
          // this.$refs.audio.play();
        }
      }
      setTimeout(() => (this.open_result = false), 5000);
      await this.$auth.fetchUser();
      await this.GET_PENDDING_ORDER();
      await this.HISTORIES_TYPE("pedding");
    },
  },
  methods: {
    ...mapMutations("trading", ["MARKET_ACTIVE"]),
    ...mapActions("trading", ["GET_PENDDING_ORDER","PLAY_AUDIO"]),
    ...mapActions("histories", ["HISTORIES_TYPE"]),
    toggleBlur() {
      this.show_blur = !this.show_blur;
    },
    toggleShowHistory(){
      this.show_history = !this.show_history;
    },
    update(v) {
      this.MARKET_ACTIVE(v);
    },
    openType(data) {
      this.type = data;
      setTimeout(() => (this.type = null), 1000);
    },
    deviceCheck() {
      let toMatch = [
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i,
      ];

      this.mobile = toMatch.some((toMatchItem) => {
        return navigator.userAgent.match(toMatchItem);
      });
    },
    async POST_PLACED(data) {
      if (this.$store.state.settings.volume) {
        // this.$refs.audio.src = placebet;
        // this.$refs.audio.volume = 1;
        // this.$refs.audio.play();
        this.PLAY_AUDIO(this.audio.place);
      }
      await this.$store.dispatch("trading/POST_PLACED", data, {
        root: true,
      });
    },
    set_height_pc() {
      if (this.$refs.control_pc) {
        this.height_pc = this.$refs.control_pc.clientHeight;
      }
    },
  },
  async mounted() {
    // this.type = 'BUY';
    // this.amount = 1000;
    // this.result = {
    //   status: "WIN",
    //   total: 1000
    // };
    this.deviceCheck();
    window.addEventListener("resize", [
      this.deviceCheck(),
      this.set_height_pc(),
    ]);
    await this.$store.dispatch("trading/GET_MARKET_LIST", null, {
      root: true,
    });
    await this.$store.dispatch("trading/GET_PENDDING_ORDER", null, {
      root: true,
    });
  },
};
</script>
