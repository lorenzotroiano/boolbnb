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
        axios.get(`http://127.0.0.1:8000/api/v1/show/${this.id}`)
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
        <ul>
            <li v-for="service in apartment.services" :key="service.id">{{ service.name }}</li>
        </ul>

    </div>
</template>




<style scoped></style>
