import _ from 'lodash';

export const state = () => ({
    chart: [],
})

export const mutations = {
    _INIT(state, payload) {
        state.chart = _.map(payload, v => [
            v.t * 1000,
            parseFloat(v.o),
            parseFloat(v.h),
            parseFloat(v.l),
            parseFloat(v.c),
            parseFloat(v.v ?? 0)
        ])
    },
    _UPDATE(state, payload) {

    }
}