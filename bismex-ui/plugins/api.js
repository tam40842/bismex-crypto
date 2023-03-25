class Request {
    url = '';
    successCallback = (res) => { };
    errorCallback = (error) => { };
    finalCallback = () => { };
    notification = false;
    loading = null;

    constructor(
        app,
        axios,
        store,
        redirect,
        route,
        config,
        toast,
    ) {
        this.app = app;
        this.axios = axios;
        this.store = store;
        this.redirect = redirect;
        this.route = route;
        this.config = config;
        this.toast = toast;
    }

    init() {
        this.axios.setBaseURL(this.config.apiUrl + '/api')
        this.axios.defaults.headers.common['Authorization'] = null;
        this.axios.onRequest((config) => {
            if (this.loading) this.store.commit('loading/STATUS', { n: this.loading, v: true }, { root: true });
            if (process.client) {
                window.$nuxt.$loading.start()
                // app.store.commit('loading/STATUS', true)
            }
            return config
        })

        this.axios.onResponse((response) => {
            if (this.loading) this.store.commit('loading/STATUS', { n: this.loading, v: false }, { root: true });
            if (process.client) {
                window.$nuxt.$loading.finish()
            }
            return response
        })

        this.axios.onResponseError((_error) => {
            if (this.loading) this.store.commit('loading/STATUS', { n: this.loading, v: false }, { root: true });
            if (process.client) {
                window.$nuxt.$loading.finish()
            }
            if (_error.response && _error.response.status === 401) {
                this.redirect({
                    name: 'login'
                })
            }
        })
    }

    async LOGIN(data) {
        await this.app.$auth.loginWith('local', { data })
            .then(async (response) => {
                this.successCallback(response);
                if (response.status != 200) {
                    if (this.notification) this.toast.error(response.data.message)
                } else {
                    switch (response.data.status) {
                        case 401:
                            if (response.status !== 200) {
                                this.toast.error(response.data.message)
                            }
                            sessionStorage.setItem('2fa', response.config.data)
                            this.redirect({
                                name: 'secure'
                            })
                            break;
                        case 422:
                            this.toast.error(response.data.message)
                            break;
                        case 503:
                            this.toast.error(response.data.message)
                            break;
                        default:
                            await this.app.$auth.fetchUser();
                            if (this.notification) this.toast.success(response.data.message)
                            this.redirect({
                                name: 'trading'
                            })
                            break;
                    }
                }
                return response
            }).catch((err) => {
                // if (this.notification) this.toast.error(err.data)
                // this.errorCallback(err)
                if (err.response.status == 503) {
                    this.toast.error(err.response.data.message)
                }
            }).finally(() => {
                this.finalCallback()
            });
    }

    GET() {
        return this.axios
            .$get(this.url)
            .then((response) => {
                this.successCallback(response)
                if (this.notification) {
                    if (response.status === 200) {
                        this.toast.success(response.data.message);
                    } else {
                        this.toast.error(response.message)
                    }
                }

                return response
            })
            .catch((_error) => {
                this.errorCallback(_error)
                if (this.notification) {
                    if (_error.response.data.message !== undefined) {
                        this.toast.error(_error.response.data.message)
                    }
                }
                if (_error.response.status == 503) {
                    this.toast.error('Website Maintenance')
                    this.redirect({
                        name: 'login'
                    })
                }
            })
            .finally(() => {
                this.finalCallback()
            })
    }
    // POST method
    POST(params) {
        return this.axios
            .$post(this.url, {
                ...params
            })
            .then((response) => {
                this.successCallback(response)
                if (this.notification) {
                    if (response.status === 200) {
                        this.toast.success(response.message);
                    } else {
                        this.toast.error(response.message)
                    }
                }
                return response
            })
            .catch((_error) => {
                this.errorCallback(_error)
                if (this.notification) {
                    if (_error.response.data.message !== undefined) {
                        this.toast.error(_error.response.data.message)
                    }
                }
                if (_error.response.status == 503) {
                    this.toast.error('Website Maintenance')
                    this.redirect({
                        name: 'login'
                    })
                }
            })
            .finally(() => {
                this.finalCallback()
            })
    }
    // Upload
    UPLOAD(params) {
        return this.axios
            .$post(this.url, params, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                this.successCallback(response)
                if (this.notification) {
                    if (response.status === 200) {
                        this.toast.success(response.message);
                    } else {
                        this.toast.error(response.message)
                    }
                }
                return response
            })
            .catch((_error) => {
                this.errorCallback(_error)
                if (this.notification) {
                    if (_error.message !== undefined) {
                        this.toast.error(_error.message)
                    }
                }
            })
            .finally(() => {
                this.finalCallback()
            })
    }

    // PUT method
    PUT(params) {
        return this.axios
            .$put(this.url, {
                ...params
            })
            .then((response) => {
                this.successCallback(response)
                if (this.notification) {
                    if (response.status === 200) {
                        this.toast.success(response.data.message);
                    } else {
                        this.toast.error(response.message)
                    }
                }
            })
            .catch((_error) => {
                this.errorCallback(_error)
                if (this.notification) {
                    if (_error.response.data.message !== undefined) {
                        this.toast.error(_error.response.data.message)
                    }
                }
            })
            .finally(() => {
                this.finalCallback()
            })
    }
    // PATCH method
    PATCH(params) {
        return this.axios.patch(this.url, {
            ...params
        })
    }
    // DELETE method
    DELETE() {
        return this.axios
            .$delete(this.url)
            .then((response) => {
                this.successCallback(response)
                if (this.notification) {
                    if (response.status === 200) {
                        this.toast.success(response.data.message);
                    } else {
                        this.toast.error(response.message)
                    }
                }
            })
            .catch((_error) => {
                this.errorCallback(_error)
                if (this.notification) {
                    if (_error.response.data.message !== undefined) {
                        this.toast.error(_error.response.data.message)
                    }
                }
            })
            .finally(() => {
                this.finalCallback()
            })
    }

}

export default ({ $axios,
    app,
    store,
    redirect,
    route,
    auth,
    $config }, inject) => {

    let api = new Request(
        app,
        $axios,
        store,
        redirect,
        route,
        $config,
        app.$toast,
    );

    api.init();

    app.$api = api;
    inject('api', api);

    function loading(nameLoading) {
        if (!nameLoading) return false;
        return store.state.loading[nameLoading];
    }

    app.$load = (v) => loading(v);
    inject('load', (v) => loading(v));
}
