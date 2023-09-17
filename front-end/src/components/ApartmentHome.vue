<script>
import axios from 'axios';

import HeaderApp from './HeaderApp.vue';

import tt from '@tomtom-international/web-sdk-maps';
import { services } from '@tomtom-international/web-sdk-services';

export default {
    name: 'ApartmentHome',
    components: {
        HeaderApp
    },
    data() {
        return {
            apartments: [],
            services: [],

            // Barra di ricerca
            search: '',
            isSearchClicked: false,
            searchValue: '',

            // Suggerimenti
            suggestions: [],
            referencePoint: null,
            selectedServices: [],
            userLocation: null,
            userCountry: null,


            // Variabili temp per il filtering
            tempRooms: null,
            tempBathrooms: null,
            tempSize: null,
            tempDistanceRange: null,

            // Variabili
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,
            distanceRange: 20,
            // Toggle per la parte di filtri avanzati
            isSidebarVisible: false,
            // Flag per vedere se ha caricato i dati
            // dataLoaded: false,
            // Flag per capire quando mettere in v-show false
            applyFilters: null,


        }
    },
    methods: {

        // COORDINATE SEARCH
        searchApartments() {
            if (this.search === '') {
                this.isSearchClicked = false; // Imposta isSearchClicked su false al clic del pulsante
            } else {
                this.isSearchClicked = true; // Imposta isSearchClicked su true al clic del pulsante
            }

            // Prende coordinate del primo suggerimento
            axios.get(`https://api.tomtom.com/search/2/geocode/${this.search}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK`)
                .then(response => {
                    const position = response.data.results[0].position;
                    this.referencePoint = new tt.LngLat(position.lon, position.lat); // Utilizza tt.LngLat per creare un oggetto LngLat
                })
                .catch(error => {
                    console.log(error);
                });
        },

        // DISTANZA date COORDINATE
        haversineDistance(coord1, coord2) {
            const toRadians = (angle) => angle * (Math.PI / 180);
            const radius = 6371; // Raggio della Terra in chilometri

            const lat1 = coord1.lat;
            const lon1 = coord1.lng;
            const lat2 = coord2.lat;
            const lon2 = coord2.lng;

            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = radius * c;

            return distance;
        },


        // FILTRAGGIO

        // FILTRI DISTANZA
        filterByDistanceRange(apartment) {
            if (!this.referencePoint) return false;

            const distance = this.haversineDistance(this.referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));

            return distance <= this.distanceRange;  // Verifica solo che la distanza sia entro il raggio impostato
        },


        updateApartments(apartments) {
            this.apartments = apartments;
        },

        handleDistanceFilter(referencePoint) {
            // Filtra gli appartamenti basandoti sulla distanza e aggiorna la lista degli appartamenti
            const filteredApartments = this.apartments.filter(apartment => {
                const distance = this.haversineDistance(referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));
                return distance <= this.distanceRange;
            });

            this.updateApartments(filteredApartments);
        },
    },

    // FILTRAGGIO ARRAY SECONDO FILTRI
    computed: {
        filteredApartments() {
            return this.apartments.filter(apartment => {
                const distanceCondition = !this.isSearchClicked || this.filterByDistanceRange(apartment);

                // Aggiungi filtro per servizi
                const servicesCondition = this.selectedServices.every(service => apartment.services.includes(service));

                // Condizione per filtrare per numero di stanze
                const roomCondition = !this.selectedRooms || apartment.rooms === this.selectedRooms;

                // Condizione per filtrare per numero di bagni
                const bathroomCondition = !this.selectedBathrooms || apartment.bathrooms === this.selectedBathrooms;

                // Condizione per filtrare per metri quadrati
                const sizeCondition = !this.tempSize || apartment.size >= this.tempSize;

                return distanceCondition && servicesCondition && bathroomCondition && sizeCondition && roomCondition;
            });
        },

    },


    // CHIAMATE AXIOS PER SERVIZI E APPARTAMENTI
    mounted() {
        axios.get('http://127.0.0.1:8001/api/v1/')
            .then(response => {
                const data = response.data;
                this.apartments = data;
                
            })
            .catch(error => {
                console.log(error);
            }),
            axios.get('http://127.0.0.1:8001/api/v1/service')
                .then(response => {
                    const data = response.data;
                    this.services = data;
                    
                })
                .catch(error => {
                    console.log(error);
                })
    },
};
</script>


<!-- TEMPLATE -->
<template>
   <HeaderApp :services="services" :selectedServices="selectedServices" :isSidebarVisible="isSidebarVisible"
        :isSearchClicked="isSearchClicked" :tempSize="tempSize" :referencePoint="referencePoint"
        :distanceRange="distanceRange" :apartments="apartments" 
        @update:distanceRange="value => distanceRange = value"
        @close-sidebar="isSidebarVisible = false"
        @toggle-sidebar="isSidebarVisible = !isSidebarVisible"
        @filter-by-distance="handleDistanceFilter"
        @apply-filters="applyFilters" @apartments-updated="updateApartments">
    </HeaderApp>
    <div class="container-fluid">

        <!-- Overlay quando SIDEBAR è TRUE -->
        <div v-if="isSidebarVisible" class="overlay" @click="isSidebarVisible = false"></div>


        <!-- LISTA APPARTAMENTI -->
        <div id="apartmentList">

            <!-- Singola card -->
            <div class="single"
                v-for="apartment in [...filteredApartments.filter(apartment => apartment.sponsor === 1), ...filteredApartments.filter(apartment => apartment.sponsor !== 1)]"
                :key="apartment.id">

                <!-- Immagine -->
                <div class="single-img">
                    <img :src="apartment.cover" :alt="apartment.cover">
                </div>

                <!-- Descrizione -->
                <div class="single-body">

                    <!-- Titolo -->
                    <div class="title">
                        <h5>{{ apartment.name }}</h5>
                    </div>

                    <!-- Bottoni e Link show -->
                    <div class="show">
                        <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }">
                            <button class="visit-button">
                                Explore
                                <i class="fa-solid fa-house"></i>
                            </button>
                        </router-link>
                    </div>

                </div>

            </div>
        </div>

    </div>
</template>

<style lang="scss" scoped>
@use '../styles/general.scss';
@use '../styles/partials/_home.scss';
@use '../styles/partials/_variables.scss' as *;
</style>
