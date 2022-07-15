<template>
    <div id="map">
        <div id="sidebar" class="sidebar flex-center left collapsed">
            <div class="sidebar-content rounded-rect flex-center">
                <sidebar @toggleProvider="toggleProvider"></sidebar>
            </div>
        </div>
    </div>
</template>



<script>
    import mapboxgl from "mapbox-gl";
    import "mapbox-gl/dist/mapbox-gl.css";
    import { onMounted } from "vue";
    import Sidebar from "./Sidebar.vue";
    import { watch, ref } from 'vue'
    import {useStore} from "vuex";

    export default {
        components: {
          Sidebar
        },
        methods: {
            toggleProvider(providerId) {
                this.$store.commit('TOGGLE_PROVIDER', providerId)
            }
        },
        setup(props) {
            const map = ref(false)
            const store = useStore()
            const markers = []

            // watch(providers, () => draw())
            // watch(providers, () => console.log(providers.value))
            // watch(datacenters, () => draw())


            store.watch(
                (state, getters) => [ getters.getDatacenters, getters.getProviders ],
                (newValue, oldValue) => {
                    draw()
                },
            );

            function draw() {
                if(store.getters.getProviders.length < 1 || store.getters.getDatacenters.length < 1 || !map.value) {
                    return
                }
                // Get the excluded providers
                let excludedProviders = []

                store.getters.getProviders.forEach(function(provider) {
                    if(!provider.enabled) {
                        excludedProviders.push(provider.code)
                    }
                })

                // Filter out the excluded one
                let datacentersToDraw = store.getters.getDatacenters.filter(function(datacenter) {
                    return !excludedProviders.includes(datacenter.provider.code)
                })

                _.forEach(markers, function(marker) {
                    marker.remove()
                })

                _.forEach(datacentersToDraw, function(datacenter) {
                    let markerColor = 'black'
                    let popup = new mapboxgl.Popup({offset: 25})
                        .setHTML('<h1>'+datacenter.provider.name+'</h1><br/><p>'+datacenter.city+' ('+datacenter.country_code+') - <strong>'+datacenter.provider_code_api+'</strong></p>')
                    let marker = new mapboxgl.Marker({ color: markerColor, rotation: 45}).setLngLat([datacenter.long, datacenter.lat]).setPopup(popup)
                    markers.push(marker)
                    marker.addTo(map.value);
                })
            }

            onMounted(() => {
                // Get available providers
                store.dispatch('fetchProviders')
                store.dispatch('fetchDatacenters')

                mapboxgl.accessToken =
                    "pk.eyJ1IjoiZ21vaWduZXUiLCJhIjoiY2w1MnZvMHo1MGd1ZDNqb2M4bWdhc2x3bCJ9.PIX7RMRtjGkFiVCU5jgXBA";
                map.value = new mapboxgl.Map({
                    container: "map",
                    style: "mapbox://styles/mapbox/light-v9",
                });
                map.value.on('load', () => {
                    draw()
                });
            })

            return {
            };
        },

    }
</script>
<style>
#map {
    height: 100vh;
}
</style>
