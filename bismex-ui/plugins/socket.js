// import { io } from 'socket.io-client';
import VueSocketIO from 'vue-socket.io'
import FlagIcon from 'vue-flag-icon'
import Vue from 'vue'

Vue.use(FlagIcon);

export default ({ app, $config, store }, inject) => {
    Vue.use(new VueSocketIO({
        debug: false,
        connection: $config.streamHost,
        reconnection: true,
        vuex: {
            store,
            actionPrefix: 'socket_',
            mutationPrefix: 'socket_'
        },
    }))
}
