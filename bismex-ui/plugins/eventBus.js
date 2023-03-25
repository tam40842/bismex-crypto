import Vue from 'vue'
const EventBus = new Vue()
export default ({
    app,
}, inject) => {
    inject('bus', EventBus)
}