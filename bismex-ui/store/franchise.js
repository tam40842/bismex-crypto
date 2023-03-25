export const state = () => ({
    commission: null,
    histories: {},
    histories_days: {}
})

export const mutations = {
    COMMISSION(state, payload) {
        state.commission = payload;
    },
    SET_HISTORIES(state, data) {
        state.histories = data;
    },
    SET_HISTORIES_DAYS(state, data) {
        state.histories_days = data;
    },
}

export const actions = {
    async Overview({ commit }) {
        const api = this.$api;
        api.url = "franchise/overview";
        api.notification = false;
        api.successCallback = (res) => {
            commit('COMMISSION', res.data)
        };
        api.errorCallback = (err) => console.error(err);
        return await api.GET();
    },
    async ACTIVE_FRANCHISE({ commit,dispatch, state }) {
        const api = this.$api;
        api.notification = true;
        api.url = "franchise/active";
        api.successCallback = async (res) => {
            if (res.status === 200) {
                await dispatch("Overview");
                await this.$auth.fetchUser();
            }
        };
        await api.POST();
    },
    async HISTORIES({ commit, state }, data) {
        const api = this.$api;
        api.notification = false;
        api.url =
            "franchise/histories?page=" +
            data.page +
            "&start_date=" +
            data.date_from +
            "&end_date=" +
            data.date_to;
        api.successCallback = (res) => {
            commit("SET_HISTORIES", res.data);
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.GET(data);
    },
    async HISTORIES_DAYS({ commit, state }, data) {
        const api = this.$api;
        api.notification = false;
        api.url =
            "franchise/historiesDays?page=" +
            data.page +
            "&start_date=" +
            data.date_from +
            "&end_date=" +
            data.date_to;
        api.successCallback = (res) => {
            commit("SET_HISTORIES_DAYS", res.data);
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.GET(data);
    },
}