export const state = () => ({
    verified: false,
    message: '',
    keys: ''
})

export const mutations = {
    loading(state, payload) {
        state.loading[payload.n] = payload.v
    },
    setVerified(state, payload) {
        state.verified = payload.verified;
        state.message = payload.message;
    },
    setKeys(state, payload) {
        state.keys = payload
    }
}

export const actions = {
    async LOGIN({ }, payload) {
        const api = this.$api;
        api.loading = 'login';
        await api.LOGIN(payload)
    },

    async TOKEN_KEYS({ commit }, type) {
        const api = this.$api;
        api.url = 'account/api-keys';
        api.notification = false;
        api.successCallback = (res) => {
            if (res.status == 200) {
                if(res.data == undefined){
                    return;
                }
                commit('setKeys', res.data)
            }
        };
        return await api.GET();
    },

    async DELETE_TOKEN({ commit }, data) {
        const api = this.$api;
        api.url = 'account/api-keys/delete/' + data;
        api.successCallback = (res) => {
            if (res.status == 200) {
                this.$toast.success(res.message)
            }
        };
        await api.POST();
    },
    
    async EDIT_TOKEN({ commit }, data) {
        const api = this.$api;
        api.url = 'account/api-keys/edit/' + data;
        api.successCallback = (res) => {
            if (res.status == 200) {
                this.$toast.success(res.message)
            }
        };
        await api.POST();
    },

    async ADD_TOKEN({ commit }, data) {
        const api = this.$api;
        api.url = 'account/api-keys/add';
        api.successCallback = (res) => {
            if (res.status == 200) {
                this.$toast.success(res.message)
            }
        };
        await api.POST();
    },

    async REGISTER({ }, payload) {
        let msg = null;
        const api = this.$api;
        api.url = 'auth/register';
        api.loading = 'register';
        api.notification = true;
        api.successCallback = (res) => { if (res.status === 200) msg = res }
        await api.POST(payload);
        return msg;
    },

    async CHANGE_PASSWORD({ commit, state }, data) {
        const api = this.$api;
        api.notification = true
        api.url = 'account/profile/changePassword';
        api.successCallback = (res) => {
            if (res.status == 200) {
                data.callback()
            }
        };
        await api.POST(data.data);
    },

    async TWO_FA({ commit, state }, data) {
        const api = this.$api;
        api.notification = true
        api.loading = "two_fa";
        api.url = 'account/profile/twofaSubmit';
        api.successCallback = (res) => {
            if (res.status == 200) {
                data.callback()
            }
        };
        await api.POST(data.data);
    },

    async ON_CHANGE_IMAGE({ commit, state }, data) {
        const api = this.$api;
        let file = data.e.target.files || data.e.dataTransfer.files;
        if (file.length) {
            let formData = new FormData();
            formData.append(data.e.target.name, data.e.target.files[0]);
            api.url = 'account/profile/ChangeImage';
            api.successCallback = async (res) => { };
            api.errorCallback = (err) => console.error(err);
            api.loading = data.l;
            await api.UPLOAD(formData);
        }
    },
    async postUploadAvatar({ commit }, data) {
        const api = this.$api;
        let file = data.e.target.files || data.e.dataTransfer.files;
        if (file.length) {
            let formData = new FormData();
            formData.append(data.e.target.name, data.e.target.files[0]);
            commit('loading/STATUS', { n: data.l, v: true }, { root: true })
            try {
                await this.$axios.post('account/profile/postUploadAvatar', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                await this.$auth.fetchUser();
            } catch (error) {
                console.error('===> Upload Avatar:', error);
            } finally {
                commit('loading/STATUS', { n: data.l, v: false }, { root: true })
            }
            // api.url = 'account/profile/postUploadAvatar';
            // api.successCallback = async (res) => await this.$auth.fetchUser();
            // api.errorCallback = (err) => console.error(err);
            // api.loading = data.l;
            // await api.UPLOAD(formData);
        }
    },
    async getKyc({ }) {
        const api = this.$api;
        api.url = 'account/profile/getKycDocument';
        api.notification = false;
        return await api.GET();
    },
    async postKyc({ }, payload) {
        const api = this.$api;
        api.url = 'account/profile/postKyc';
        api.loading = 'kyc';
        api.notification = true;
        api.successCallback = async (res) => {
            await this.$auth.fetchUser()
        };
        api.errorCallback = (err) => console.error(err);
        return await api.POST(payload);
    },
    async postForgot({ }, data) {
        const api = this.$api;
        api.url = 'auth/resetEmail';
        api.loading = 'login';
        api.notification = false;
        return await api.POST(data);
    },
    async postReset({ }, data) {
        const api = this.$api;
        api.url = 'auth/resetPassword';
        api.loading = 'login';
        api.notification = true;
        api.successCallback = (res) => this.$router.push('/login');
        api.errorCallback = (err) => console.error(err);
        await api.POST(data);
    },
    async getVerify({ commit }, token) {
        const api = this.$api;
        api.url = 'auth/verify_email?token=' + token;
        api.notification = false;
        api.successCallback = (res) => {
            let status = (res.status == 200) ? true : false;
            commit('setVerified', {
                verified: status,
                message: res.message
            });
        };
        api.errorCallback = (err) => console.error(err);
        return await api.GET(token);
    },
}
