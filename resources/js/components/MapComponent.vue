<template>
    <div>
    <div id="map">
<!--        <div class="absolute z-40 vh-100 w-full top-0 p-2">-->
<!--            <div id="title" class="px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white"><h1 class="flex-1 align-items-center justify-center text-center text-l">Cloud datacenters carbon intensity</h1></div>-->
<!--        </div>-->
        <div class="absolute z-40 vh-100 w-full bottom-0 p-2 content-center text-center">
            <div id="sidebarButton" :class="{ hidden: this.isSidebarOpened }" class="">
                <button type="button" @click="toggleSidebar()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"><AdjustmentsIcon class="h-5 w-5 text-gray-500 mr-2"/> Select cloud providers</button>
            </div>
        </div>
        <div id="sidebar" class="sidebar z-50 hidden flex-center left collapsed">
            <div class="sidebar-content z-50 rounded-rect flex-center">
                <sidebar @toggleProvider="toggleProvider" :open="isSidebarOpened" :toggle="toggleSidebar" ref="sidebar"></sidebar>
            </div>
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
                    let clightColor = 'text-gray-300'
                    let cdarkColor = 'text-gray-600'
                    let flightColor = 'text-gray-300'
                    let fdarkColor = 'text-gray-600'

                    let html = '<h1 class="text-l font-bold">'+datacenter.provider.name+'</h1>'+
                        '<p class="mb-4">' +datacenter.city+' ('+datacenter.country_code+') - <strong>'+datacenter.provider_code_api + '</strong></p>'

                    if(datacenter.usage) {

                        if (datacenter.usage.carbonIntensity <= 100) {
                            markerColor = '#4ade80'
                            clightColor = 'green-300'
                            cdarkColor = 'green-600'
                        } else if (datacenter.usage.carbonIntensity <= 300) {
                            markerColor = '#fbbf24'
                            clightColor = 'amber-300'
                            cdarkColor = 'amber-600'
                        } else if (datacenter.usage.carbonIntensity <= 600) {
                            markerColor = '#fb923c'
                            clightColor = 'orange-300'
                            cdarkColor = 'orange-600'
                        } else {
                            markerColor = '#ef4444'
                            clightColor = 'red-300'
                            cdarkColor = 'red-600'
                        }

                        if (datacenter.usage.fossilFuelPercentage <= 25) {
                            flightColor = 'green-300'
                            fdarkColor = 'green-600'
                        } else if (datacenter.usage.fossilFuelPercentage <= 50) {
                            flightColor = 'amber-300'
                            fdarkColor = 'amber-600'
                        } else if (datacenter.usage.fossilFuelPercentage <= 75) {
                            flightColor = 'orange-300'
                            fdarkColor = 'orange-600'
                        } else {
                            flightColor = 'red-300'
                            fdarkColor = 'red-600'
                        }

                        html += '<div class="grid grid-cols-2 gap-4">\n' +
                            '                <div>\n' +
                            '                    <span class="inline-flex items-center h-10 px-6 py-6 rounded-md text-xl font-medium bg-'+clightColor+' text-'+cdarkColor+'">\n' +
                            '                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-'+cdarkColor+'" fill="currentColor" viewBox="0 0 8 8">\n' +
                            '                          <circle cx="4" cy="4" r="3" />\n' +
                            '                        </svg>\n' +
                            '                        '+datacenter.usage.carbonIntensity+'g\n' +
                            '                    </span>\n' +
                            '                   <p class="text-center">Carbon intensity</p>\n' +
                            '               </div>\n' +
                            '               <div>\n' +
                            '                    <span class="inline-flex items-center h-10 px-6 py-6 rounded-md text-xl font-medium bg-'+flightColor+' text-'+fdarkColor+'">\n' +
                            '                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-'+fdarkColor+'" fill="currentColor" viewBox="0 0 8 8">\n' +
                            '                          <circle cx="4" cy="4" r="3" />\n' +
                            '                        </svg>\n' +
                            '                        '+Math.round(datacenter.usage.fossilFuelPercentage)+'%\n' +
                            '                    </span>\n' +
                            '                   <p class="text-center">Fossil fuel</p>\n' +
                            '               </div>\n' +
                            '           </div>'



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

#sidebar {
    z-index: 9999;
}
#title {
    z-index: 9999;
}

.mapboxgl-popup-close-button {
    right: 4px;
}

</style>
