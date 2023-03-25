export const state = () => ({
  histories: {},
  borrow: 0,
  package_detail: {},
  overview: {},
});

export const mutations = {
  SET_OVERVIEW(state, payload) {
    state.overview = payload;
  },
  SET_HISTORIES(state, data) {
    state.histories = data;
  },
};
export const actions = {
  async Overview({ commit }) {
    const api = this.$api;
    api.url = "autotrade/overview";
    api.notification = false;
    api.successCallback = (res) => {
      let status = res.status == 200 ? true : false;
      commit("SET_OVERVIEW", {
        package_detail: res.data.package,
        autotrade_commission: res.data.commission,
        link_ref: res.data.link_ref,
        profit: res.data.profit,
      });
    };
    api.errorCallback = (err) => console.error(err);
    return await api.GET();
  },
  async BUY_PACKAGE({ dispatch, state }) {
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/buyPackage";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST();
  },
  async Swap({ dispatch, state }, payload) {
    let data = {};
    data.amount = payload.amount;
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/swap";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        payload.callback();
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST(data);
  },
  async Borrow({ dispatch, state }, payload) {
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/borrow";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        payload.callback();
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST();
  },
  async ActiveBot({ dispatch, state }) {
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/activeBot";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        this.$toast.success(res.message);
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST();
  },
  async Pay({ dispatch, state }, payload) {
    let data = {};
    data.amount = payload.amount;
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/pay";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        payload.callback();
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST(data);
  },
  async GET({ dispatch, state }, payload) {
    let data = {};
    data.amount = payload.amount;
    const api = this.$api;
    api.notification = true;
    api.url = "autotrade/getCom";
    api.successCallback = async (res) => {
      if (res.status === 200) {
        payload.callback();
        await dispatch("Overview");
        await this.$auth.fetchUser();
      }
    };
    await api.POST(data);
  },
  async HISTORIES({ commit, state }, data) {
    const api = this.$api;
    api.notification = false;
    api.url =
      "autotrade/histories?page=" +
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
};
