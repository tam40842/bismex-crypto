import { each, forOwnRight } from "lodash";

export const state = () => ({
  histories_orders: [],
  histories_withdraw: [],
  histories_deposit: [],
  bot_orders: {},
  waiting: 0,
  profit: 0,
  begin: 0,
  format_profit: '',
})

export const mutations = {
  HISTORIES_TYPE(state, payload) {
    state.histories_orders = [];
    state.histories_orders = payload.histories_orders;
    state.histories_withdraw = payload.histories_withdraw;
    state.histories_deposit = payload.histories_deposit;
    state.waiting = payload.waiting;
    state.profit = payload.profit;
    state.begin = payload.begin;
    if(payload.profit > 0) {
      state.format_profit = '+'
    }else if(payload.profit < 0) {
      state.format_profit = '-'
    }else {
      state.format_profit = ''
    }
  },
  BOT_ORDERS(state, payload) {
    state.histories_orders = [];
    state.bot_orders = payload;
  },
  ORDER(state, payload) {
    state.histories_orders.unshift(payload)
  }
}
export const actions = {
  async HISTORIES_TYPE({ commit }, type) {
    await this.$axios.get('histories/time/' + type).then(async (result) => {
      commit('HISTORIES_TYPE', result.data.data)
    }).catch((err) => {
      // this.$toast.error(err.message)
    });
  },
  async BOT_ORDERS({ commit }, data) {
    await this.$axios.get('histories/bot-orders').then(async (result) => {
      commit('BOT_ORDERS', result.data.data)
    }).catch((err) => {
      // this.$toast.error(err.message)
    });
  },

  async ORDER({ commit }, data) {
    await commit('ORDER', data.order)
  }
}