export const state = () => ({
    accept: false,
})

export const mutations = {
    ACCEPT(state, payload) {
        state.accept = payload;
    }
}