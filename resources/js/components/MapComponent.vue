<template>
    <div id="map">
        <div id="title" class="px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white"><h1 class="text-l">Cloud datacenters carbon intensity</h1></div>
        <div id="sidebar" class="sidebar flex-center left collapsed">
            <div class="sidebar-content rounded-rect flex-center">
                <div id="sidebarButton" :class="{ hidden: this.isSidebarOpened }">
                    <button type="button" @click="toggleSidebar()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"><AdjustmentsIcon class="h-5 w-5 text-gray-500 mr-2"/> Select cloud providers</button>
                </div>
                <sidebar @toggleProvider="toggleProvider" :open="isSidebarOpened" :toggle="toggleSidebar" ref="sidebar"></sidebar>
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
    import { AdjustmentsIcon } from "@heroicons/vue/outline"

    export default {
        components: {
            Sidebar,
            AdjustmentsIcon
        },
        data() {
            return {
                isSidebarOpened: false
            }
        },
        methods: {
            toggleProvider(providerId) {
                this.$store.commit('TOGGLE_PROVIDER', providerId)
            },
            toggleSidebar() {
                this.isSidebarOpened = !this.isSidebarOpened
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
                    let markerColor = '#d4d4d8'

                    let html = '<h1 class="text-l font-bold">'+datacenter.provider.name+'</h1>'+
                        '<p>' +datacenter.city+' ('+datacenter.country_code+') - <strong>'+datacenter.provider_code_api + '</strong></p>'

                    if(datacenter.usage) {
                        html += '<p>Carbon intensity: '+datacenter.usage.carbonIntensity+' '+datacenter.usage.units+'</p>'
                        html += '<p>Fossil Fuel: '+datacenter.usage.fossilFuelPercentage+'%</p>'

                        if (datacenter.usage.carbonIntensity <= 100) {
                            markerColor = '#84cc16'
                        } else if (datacenter.usage.carbonIntensity <= 300) {
                            markerColor = '#fbbf24'
                        } else if (datacenter.usage.carbonIntensity <= 600) {
                            markerColor = '#dc2626'
                        } else {
                            markerColor = '#991b1b'
                        }
                    } else {
                        html += '<p>Carbon intensity: Not available</p>'
                        html += '<p>Fossil Fuel: Not available</p>'
                    }



                    let popup = new mapboxgl.Popup({offset: 25})
                        .setHTML(html)
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
.hidden {
    display: none;
}
#sidebarButton {
    position: absolute;
    right: 10px;
    top: 10px;
    z-index: 9999;
}

#title {
    position: absolute;
    left: 10px;
    top: 10px;
    z-index: 9999;
}
</style>
