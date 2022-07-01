<template>
    <div id="map">
    </div>
</template>



<script>
    import mapboxgl from "mapbox-gl";
    import "mapbox-gl/dist/mapbox-gl.css";
    import { onMounted } from "vue";
    import {forEach} from "lodash/collection";

    export default {
        data() {
            return {
                datacenters: {},
            }
        },
        setup() {
            onMounted(() => {
                mapboxgl.accessToken =
                    "pk.eyJ1IjoiZ21vaWduZXUiLCJhIjoiY2w1MnZvMHo1MGd1ZDNqb2M4bWdhc2x3bCJ9.PIX7RMRtjGkFiVCU5jgXBA";
                const map = new mapboxgl.Map({
                    container: "map",
                    style: "mapbox://styles/mapbox/light-v9",
                });
                map.on('load', () => {
                    axios.get('/api/datacenters')
                        .then((response)=>{
                            let datacenters = response.data.data
                            console.log(datacenters)
                            _.forEach(datacenters, function(datacenter) {
                                // const el = document.createElement('div');
                                // el.className = 'marker';

                                // make a marker for each feature and add to the map

                                let markerColor = 'black'

                                if(datacenter.provider.code === 'aws') markerColor = '#db9815'
                                if(datacenter.provider.code === 'azure') markerColor = '#1b5281'
                                if(datacenter.provider.code === 'orange') markerColor = '#db4d15'
                                if(datacenter.provider.code === 'gcp') markerColor = '#1580db'
                                if(datacenter.provider.code === 'alibaba') markerColor = 'brown'
                                if(datacenter.provider.code === 'interoute') markerColor = '#6f2676'
                                if(datacenter.provider.code === 'digitalocean') markerColor = '#092944'
                                if(datacenter.provider.code === 'rackspace') markerColor = '#ba1313'
                                if(datacenter.provider.code === 'upcloud') markerColor = '#442676'
                                //if(datacenter.provider.code === 'platformsh') markerColor = 'black'
                                if(datacenter.provider.code === 'oracle') markerColor = '#9b9b9b'

                                new mapboxgl.Marker({ color: markerColor, rotation: 45}).setLngLat([datacenter.long, datacenter.lat]).addTo(map);
                            })
                        })
                    // TODO: Here we want to load a layer
                    // TODO: Here we want to load/setup the popup
                });
            });
            return {};
        },
    }
</script>
<style>
#map {
    height: 100vh;
}
</style>
