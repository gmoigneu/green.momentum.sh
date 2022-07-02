/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue'
import { createStore } from "vuex";
import ExampleComponent from "./components/MapComponent.vue";

const store = createStore({
    state: {
        providers: [],
        datacenters: [],
        regions: []
    },
    getters: {
        getProviders: (state) => state.providers,
        getDatacenters: (state) => state.datacenters,
    },
    actions: {
        async fetchDatacenters({ commit }) {
            try {
                const data = await axios.get('/api/datacenters')
                commit('SET_DATACENTERS', data.data.data)
            }
            catch (error) {
                alert(error)
                console.log(error)
            }
        },
        async fetchProviders({ commit }) {
            try {
                const data = await axios.get('/api/providers')
                    .then((response) => {
                        return response.data.data.map(function(value) {
                            // Add enabled = true
                            value.enabled = true
                            return value
                        })
                    })
                commit('SET_PROVIDERS', data)
            }
            catch (error) {
                alert(error)
                console.log(error)
            }
        }
    },
    mutations: {
        SET_PROVIDERS(state, providers) {
            state.providers = providers
        },
        SET_DATACENTERS(state, datacenters) {
            state.datacenters = datacenters
        }
    }
});

const app = createApp(ExampleComponent)
app.use(store)
app.mount('#app')
