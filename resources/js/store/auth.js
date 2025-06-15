import axios from 'axios'
import router from '@/router'
export default {
    namespaced: true,
    state:{
        authenticated:false,
        user:{},
        password_reset: false,
        // password_reset: localStorage.getItem('password_reset') === 'true',
    },
    getters:{
        authenticated(state){
            return state.authenticated
        },
        user(state){
            return state.user
        },
        password_reset(state){
            return state.password_reset
        }
    },
    mutations:{
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_USER (state, value) {
            state.user = value
        },
        SET_PASSWORD_RESET (state, value) {
            state.password_reset = value
        }
    },
    actions:{
        login({commit}){
            return axios.get('/api/user').then(({data})=>{
                commit('SET_USER',data)
                commit('SET_AUTHENTICATED',true)
                location.reload();
            }).catch((error)=>{
                console.log(error);
                
                commit('SET_USER',{})
                commit('SET_AUTHENTICATED',false)
            })
        },
        logout({commit}){
            commit('SET_USER',{})
            commit('SET_AUTHENTICATED',false)
        }
    }
}