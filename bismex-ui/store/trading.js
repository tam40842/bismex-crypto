import {Howl} from 'howler';
const srcBettick = "/media/bettick.mp3";
const srcHeartbeat = "/media/heartbeat.mp3";
const srcWin = "/media/win2.mp3";
const srcStart = "/media/start.mp3";
const srcAPlaceBet = "/media/placebet.mp3";

export const state = () => ({
    market_active: null,
    market_list: [],
    order: {
        BUY: 0,
        SELL: 0,
    },
    mode: '',
    amount: 10,
    history_tab: '',
    audio: {
      bettick: srcBettick,
      win: srcWin,
      heartbeat: srcHeartbeat,
      start: srcStart,
      place: srcAPlaceBet,
    }
})

export const mutations = {
    MARKET_LIST(state, payload) {
        state.market_list = payload;
    },
    MARKET_ACTIVE(state, payload) {
        state.market_active = payload;
    },
    ORDER(state, payload) {
        state.order = payload;
    },
    MODE(state, payload) {
        state.mode = payload;
    },
    AMOUNT(state, payload) {
        state.amount = payload
    }
}
export const actions = {
    PLAY_AUDIO({ commit }, data){
      let sound = new Howl({
        src: [data],
        html5: true,
        volume: 0.5,
      });
      sound.play();
    },
    async GET_MARKET_LIST({ commit }) {
        await this.$axios.get('markets/list').then((res) => {
            commit('MARKET_LIST', res.data.data.markets)
            commit('MARKET_ACTIVE', res.data.data.market_active)
        }).catch((err) => {

        });
    },
    async CHANGE_MODE({ commit }, data) {
        commit('loading/STATUS', { n: 'playmode', v: true }, { root: true })
        await this.$axios.post('trading/changePlayMode', { playmode: data }).then(async (result) => {
            this.$toast.success(result.message)
            await this.$auth.fetchUser();
            commit('loading/STATUS', { n: 'playmode', v: false }, { root: true })
        }).catch((err) => {
            this.$toast.error(err.message)
            commit('loading/STATUS', { n: 'playmode', v: false }, { root: true })
        });
    },
    async GET_PENDDING_ORDER({ commit }) {
        const api = this.$api;
        api.url = 'trading/getPendingOrder';
        api.notification = false;
        api.successCallback = (res) => {
            commit('ORDER', res.data)
        }
        await api.GET();
    },
    async POST_PLACED({ state, commit, dispatch }, payload) {
        let data = {};
        data['mode'] = 'basic';
        data['expired_at'] = 1;
        data['amount'] = state.amount;
        data['market_name'] = state.market_active.market_name;
        data['action'] = payload;
        if (state.amount > 0) {
            commit('loading/STATUS', { n: payload, v: true }, { root: true })
            await this.$axios.post('trading/placed', data).then(async (result) => {
                if (result.data.status === 422) {
                    this.$toast.error(result.data.message)
                } else {
                    this.$toast.success(result.data.message)
                    await this.$auth.fetchUser();
                    await dispatch('GET_PENDDING_ORDER');
                }
                commit('loading/STATUS', { n: payload, v: false }, { root: true })
            }).catch((err) => {
                this.$toast.error(err.message)
                commit('loading/STATUS', { n: payload, v: false }, { root: true })
            });
        }
    }
}
