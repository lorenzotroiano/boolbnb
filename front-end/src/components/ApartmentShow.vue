<script>
import axios from 'axios';

export default {
    data() {
        return {
            apartment: [],
            map: null,
            center: [4, 44.4],
            apiKey: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
        };
    },

    methods: {
        initializeMap() {
            this.map = new tt.map({
                key: this.apiKey,
                container: this.$refs.map,
                center: this.center,
                zoom: 10,
            });

            this.map.on("load", () => {
                new tt.Marker().setLngLat(this.center).addTo(this.map);
            });
        },
    },
    props: ['id'],
    mounted() {
        axios.get(`http://127.0.0.1:8000/api/v1/show/${this.id}`)
            .then(response => {
                this.apartment = response.data.apartment;
            })
            .catch(error => {
                console.log(error);
            })
        this.initializeMap();
    },
};
</script>

<template>
    <div ref="map" style="width: 80%; height: 50%;"></div>

    <div>
        <h1>Nome: {{ apartment.name }}</h1>
        <p>ID: {{ apartment.id }}</p>
        <p>Descrizione: {{ apartment.description }}</p>
        <p>Stanze: {{ apartment.room }}</p>
        <p>Bagni: {{ apartment.bathroom }}</p>
        <p>Metri quadrati: {{ apartment.mq }}</p>
        <p>Indirizzo: {{ apartment.address }}</p>
        <p>Latitudine: {{ apartment.latitude }}</p>
        <p>Longitudine: {{ apartment.longitude }}</p>
        <p>Visibile: {{ apartment.visible }}</p>
        <p>Sponsorizzato: {{ apartment.sponsor }}</p>
        <img :src="apartment.cover" alt="Copertina dell'appartamento">




        <!-- Servizi -->
        <h2>Servizi:</h2>
        <div class="container">
            <div class="row">
                <div class="col-md-4" v-for="service in apartment.services" :key="service.id">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <i :class="service.icon" class="service-icon"></i>
                            <p class="card-text">{{ service.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</template>




<style scoped>
.service-icon {
    font-size: 24px;

}


.card:hover {
    background-color: #007bff;

    color: #fff;

}
</style>
