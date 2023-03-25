import { each, forOwnRight } from "lodash";

export const state = () => ({
    currencies: {},
    histories_deposit: {},
    histories_withdraw: {},
    histories_transfer: {},
    histories_transactions: {},
    commission_transaction: {},
})

export const mutations = {
    CURRENCIES(state, payload) {
        state.currencies = payload;
    },
    HISTORIES_DEPOSIT(state, data) {
        state.histories_deposit = data;
    },
    HISTORIES_WITHDRAW(state, data) {
        state.histories_withdraw = data;
    },
    HISTORIES_TRANSFER(state, data) {
        state.histories_transfer = data;
    },
    TRANSACTION(state, data) {
        state.histories_transactions = data.transaction;
        state.commission_transaction = data.commission_transaction;
    } 
}
export const actions = {
    async CURRENCIES({ commit, state }) {
        const api = this.$api;
        api.notification = false
        api.url = 'wallet/getCurrencies';
        api.successCallback = (res) => {
            res.data.currencies.forEach(value => {
                if (value.symbol == 'USDT') {
                    commit('CURRENCIES', value)
                }
            });
        };
        await api.GET();
    },

    async OVERVIEW({ commit, state }, data) {
      const api = this.$api;
      api.loading = 'overview';
      api.notification = true
      api.url = 'wallet/overview/postOverview';
      api.successCallback = (res) => {
        if (res.status == 200) {
            data.callback()
        }
      };
      api.errorCallback = (err) => this.$toast.error(err.response.data.message);
      await api.POST(data.data);
  },

    async HISTORIES_DEPOSIT({ commit, state }, data) {
        const api = this.$api;
        api.notification = false
        api.url = 'wallet/deposit/getHistories?page=' + data.page;
        api.successCallback = (res) => {
            commit('HISTORIES_DEPOSIT', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },

    async HISTORIES_WITHDRAW({ commit, state }, data) {
        const api = this.$api;
        api.notification = false
        api.url = 'wallet/withdraw/getHistories?page' + data.page;
        api.successCallback = (res) => {
            commit('HISTORIES_WITHDRAW', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },

    async HISTORIES_TRANSFER({ commit, state }, data) {
        const api = this.$api;
        api.notification = false
        api.url = 'wallet/transfer/getHistories?page' + data.page;
        api.successCallback = (res) => {
            commit('HISTORIES_TRANSFER', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },

    async POST_WITHDRAW({ commit, state }, data) {
        const api = this.$api;
        api.url = 'wallet/withdraw/postWithdraw';
        api.loading = 'withdraw';
        api.notification = true
        api.successCallback = (res) => {
            if (res.status == 200) {
                data.callback()
            }
        };
        await api.POST(data.data);
    },

    async POST_TRANSFER({ commit, state }, data) {
        const api = this.$api;
        api.url = 'wallet/transfer/postTransfer';
        api.loading = 'transfer';
        api.notification = true
        api.successCallback = (res) => {
            if (res.status == 200) {
                data.callback()
            }
        };
        await api.POST(data.data);
    },

    async TRANSACTION({ commit }, data) {
        const api = this.$api;
        api.notification = true
        api.url = 'wallet/overview/transactions?page' + data.page;
        api.successCallback = (res) => {
            commit('TRANSACTION', res.data)
        };
        api.errorCallback = (err) => this.$toast.error(err.response.data.message);
        await api.POST(data);
    },
}
