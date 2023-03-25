import { each, forOwnRight } from "lodash";

export const state = () => ({
    commission: null,
    histories: {},
    data_f1_date: [],
    data_f1_volume: [],
    total_volume: 0,
    statistics: [],
    detail: {},
    histories_orders: [],
    volume_f1: []
})

export const mutations = {
    COMMISSION(state, payload) {
        state.commission = payload;
    },
    HISTORIES(state, payload) {
        state.histories = payload;
    },
    GET_MEMBER(state, payload) {
        state.data_f1_date = payload.data_f1_date;
        state.data_f1_volume = payload.data_f1_volume
        state.total_volume = payload.total_volume
        state.statistics = payload.statistics
    },
    DETAIL_COMMISSION(state, payload) {
        state.detail = payload.detail;
        state.histories_orders = payload.histories_orders;
    },
    TOTAL_VOLUME_F1(state, payload) {
        state.volume_f1 = payload;
    }
}
export const actions = {
    async OVERVIEW({ commit, state }) {
        const api = this.$api;
        api.notification = false
        api.url = 'commission/overview';
        api.successCallback = (res) => {
            commit('COMMISSION', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.GET();
    },

    async HISTORIES({ commit, state }, data) {
        const api = this.$api;
        api.notification = false
        api.url = 'commission/histories?page' + data.page;
        api.successCallback = (res) => {
            commit('HISTORIES', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },

    async GET_MEMBER({ commit }, payload) {
        commit('loading/STATUS', { n: 'member', v: true }, { root: true })
        const api = this.$api;
        api.notification = false
        api.url = `commission/tree/${payload.email}?page=${payload.page}`;
        api.successCallback = (res) => {
            if (res.status != 200) {
                this.$toast.error(res.message)
                return;
            }
            payload.callback();
            commit('GET_MEMBER', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err);
        api.finalCallback = () => commit('loading/STATUS', { n: 'member', v: false }, { root: true })
        await api.GET();
    },

    async DETAIL_COMMISSION({ commit }, id) {
        const api = this.$api;
        api.notification = false
        api.url = 'commission/detail/' + id;
        api.successCallback = (res) => {
            if (res.status == 200) {
                commit('DETAIL_COMMISSION', res.data)
            } else {
                api.redirect({
                    name: 'commission'
                })
            }
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.GET();
    },

    async POST_TOTAL_VOLUME({ commit, state }, data) {
        const api = this.$api;
        api.notification = false
        api.url = 'commission/total-volume-f1';
        api.successCallback = (res) => {
            commit('TOTAL_VOLUME_F1', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },
}
