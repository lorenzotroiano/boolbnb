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
        axios.get(`http://127.0.0.1:8001/api/v1/show/${this.id}`)
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
        <!-- <h4 class="mb-3">Servizi:</h4>
        <ul class="list-group mb-4">
            <li class="list-group-item d-flex justify-content-between align-items-center"
                v-for="service in apartment.services" :key="service.id">
                {{ service.name }}
                <span class="badge badge-primary badge-pill">{{ service.icon }}</span>
            </li>
        </ul> -->

    </div>
</template>

  
  

<style scoped></style>
