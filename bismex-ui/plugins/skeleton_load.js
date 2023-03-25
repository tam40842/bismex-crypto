export default ({ store, app }, inject) => {
    function skeleton(n, v) {
        store.commit('settings/SKELETON', { n, v }, { root: true })
    }
    inject('skeleton', skeleton)
    app.$skeleton = skeleton;

    inject('getSkeleton', store.state.settings.skeleton)
    app.$getSkeleton = store.state.settings.skeleton;
}