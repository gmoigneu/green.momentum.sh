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
                // provider = providers.find(o => o.id === providerId);
                // provider.enabled = false
            }
        },
        setup(props) {
            const map = ref(false)
            const store = useStore();

            // watch(providers, () => draw())
            // watch(providers, () => console.log(providers.value))
            // watch(datacenters, () => draw())

            function draw() {
                console.log("DRAW")
                if(store.state.providers.length < 1 || !store.state.datacenters.length < 1 || !map.value) {
                    return
                } else {
                    console.log("Drawing boss")
                }
                // Get the excluded providers
                let excludedProviders = []

                store.state.providers.forEach(function(provider) {
                    if(!provider.enabled) {
                        excludedProviders.push(provider.code)
                    }
                })

                // Filter out the excluded one
                let datacentersToDraw = store.state.datacenters.filter(function(datacenter) {
                    return !excludedProviders.includes(datacenter.provider.code)
                })

                _.forEach(datacentersToDraw, function(datacenter) {
                    let markerColor = 'black'
                    let popup = new mapboxgl.Popup({offset: 25})
                        .setHTML('<h1>'+datacenter.provider.name+'</h1><br/><p>'+datacenter.city+' ('+datacenter.country_code+') - <strong>'+datacenter.provider_code_api+'</strong></p>')
                    new mapboxgl.Marker({ color: markerColor, rotation: 45}).setLngLat([datacenter.long, datacenter.lat]).setPopup(popup).addTo(map.value);
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
