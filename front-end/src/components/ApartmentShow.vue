<script>
import axios from 'axios';
import tt from '@tomtom-international/web-sdk-maps';
export default {
    data() {
        return {
            apartment: [],
            map: null,
            apiKey: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
        };
    },

    methods: {
        initializeTomTomMap() {
            try {
                this.map = tt.map({
                    key: this.apiKey,
                    container: this.$refs.map, // Usa il riferimento al div della mappa
                    center: this.center, // Utilizza this.center come centro iniziale
                    zoom: 10,
                });

                this.map.on('load', () => {
                    new tt.Marker().setLngLat(this.center).addTo(this.map); // Utilizza this.center come posizione del marcatore
                });
            } catch (error) {
                console.error("Errore durante l'inizializzazione della mappa TomTom:", error);
            }
        },
    },
    props: ['id'],
    mounted() {
        axios.get(`http://127.0.0.1:8001/api/v1/show/${this.id}`)
            .then(response => {
                this.apartment = response.data.apartment;
                this.center = [this.apartment.latitude, this.apartment.longitude];
            })
            .catch(error => {
                console.log(error);
            })
        this.initializeTomTomMap();
    },
};
</script>

<template>
    <div ref="map" style="width: 50%; height: 400px;"></div>

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
