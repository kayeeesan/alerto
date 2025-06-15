import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from '@/store/auth'

const store = createStore({
    modules: {
        auth
    },
    plugins: [
        createPersistedState({
            key: 'my-app-auth',         // name used in localStorage
            paths: ['auth'],            // only persist the auth module
            storage: window.localStorage,
        })
    ]
})

export default store
