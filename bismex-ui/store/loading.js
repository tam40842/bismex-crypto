export const state = () => ({
    login: false,
    register: false,
    forgot: false,
    buy: false,
    sell: false,
    transfer: false,
    transfer_user: false,
    withdraw: false,
    two_fa: false,
    change_password: false,
    avatar: false,
    kyc: false,
    identity_backend: false,
    identity_frontend: false,
    selfie: false,
    network: false,
    support: false,
    playmode: false,
    member: false,
    autotrade: false,
    franchise: false,
})
export const mutations = {
    STATUS(state, payload) {
        state[payload.n] = payload.v
    }
}