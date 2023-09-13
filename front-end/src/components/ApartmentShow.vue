<script>
import axios from 'axios';

export default {
    data() {
        return {
            apartment: [],

        }
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
    },
};



</script>

<template>
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
