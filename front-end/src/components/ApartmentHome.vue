<script>
import axios from 'axios';

export default {
    name: 'ApartmentHome',
    data() {
        return {
            apartments: [],
            search: '',
        }
    },
    computed: {
        filteredApartments() {
            // Creo un nuovo array filtrato
            return this.apartments.filter(apartment => {
                // Restituisce TRUE se i valori sono verificati e FALSE se non lo sono
                return apartment.name.toLowerCase().includes(this.search.toLowerCase());
            });
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

    <!-- Creo la barra di ricerca e imposto v-model sulla variabile search -->
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Cerca..." aria-label="Cerca..."
            aria-describedby="button-addon2" v-model="search">
    </div>

    <!-- Lista degli appartamenti -->
    <ul>
        <li v-for="apartment in filteredApartments" :key="apartment.id">
            <!-- Link alla show -->
            <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }" class="link">
                <h2>{{ apartment.name }}</h2>
            </router-link>
        </li>
    </ul>
</template>

<style scoped></style>
