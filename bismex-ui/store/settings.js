export const state = () => ({
    skeleton: false,
    height: 'auto',
    ios: false,
    orientation: false,
    scroll: true,
    volume: false,
})

export const mutations = {
    SKELETON(state, payload) {
        state.skeleton = payload;
    },
    SET_HEIGHT(state, payload) {
        state.height = payload
    },
    SET_IOS(state, payload) {
        state.ios = payload
    },
    SET_ORIENTATION(state, payload) {
        state.orientation = payload
    },
    SET_SCROLL(state, payload) {
        state.scroll = payload
    },
    SET_VOLUME(state, payload) {
        state.volume = payload;
    }
};