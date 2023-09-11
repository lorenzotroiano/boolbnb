<script>
import axios from 'axios';

export default {
    name: 'ApartmentHome',
    data() {
        return {
            apartments: [],

        }
    },
    mounted() {
        axios.get('http://127.0.0.1:8000/api/v1/')
            .then(response => {
                const data = response.data;
                this.apartments = data;
            })
            .catch(error => {
                console.log(error);
            })
    },
};



</script>

<template>
    <h1>
        Lista appartamenti:
    </h1>
    <ul>
        <li v-for="apartment in apartments" :key="apartment.id">
            <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }" class="link">
                <h2>{{ apartment.name }}</h2>
            </router-link>
        </li>
    </ul>
</template>

<style scoped></style>
