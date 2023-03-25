export const state = () => ({
	tickets: null,
	detail: null,
});

export const mutations = {
	tickets(state, payload) {
		state.tickets = payload;
	},
	detail(state, payload) {
		state.detail = payload;
	},
};
export const actions = {
	async SEND_TICKET({ dispatch, commit }, data) {
		commit("loading/STATUS", { n: "support", v: true }, { root: true });
		try {
			let res = await this.$axios.post("support/create", data.json());
			this.$toast.success(res.data.message);
			await dispatch("getTickets");
			data.clear();
		} catch (error) {
			this.$toast.error(res.data.message);
		} finally {
			commit("loading/STATUS", { n: "support", v: false }, { root: true });
		}
	},
	async getTickets({ commit }, page = 1) {
		const api = this.$api;
		api.url = "support/tickets?page=" + page;
		api.notification = false;
		api.successCallback = (res) => commit("tickets", res.data);
		await api.GET();
	},
	async getDetail({ commit, state }, data) {
		if (state.detailt && state.detailt.ticketid !== data.ticketid)
			commit("detail", null);
		const api = this.$api;
		api.notification = false;
		api.loading = "support";
		api.url = "support/ticket/" + data.ticketid + "?page=" + data.page;
		api.successCallback = (res) => {
			commit("detail", res.data);
		};
		await api.GET();
	},
	async reply({ dispatch }, map) {
		let data = map.data.json();
		data["ticketid"] = map.id;
		const api = this.$api;
		api.url = "support/ticket/reply";
		api.notification = true;
		api.successCallback = async (res) => {
			await dispatch("getDetail", { ticketid: map.id, page: 1 });
			map.data.clear();
		};
		await api.POST(data);
	},
};
