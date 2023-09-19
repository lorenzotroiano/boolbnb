<script>
import axios from 'axios';

import HeaderApp from './HeaderApp.vue';

import { services } from '@tomtom-international/web-sdk-services';

export default {
    name: 'ApartmentHome',
    components: {
        HeaderApp
    },
    data() {
        return {
            // Appartamenti e servizi
            originalApartments: [],
            apartments: [],
            services: [],

            // Distanza
            currentCoordinates: null,

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
            // Toggle per la parte di filtri avanzati
            isSidebarVisible: false,
            
            applyFilters: null,


        }
    },
    methods: {

        getImageUrl(imageName) {
            return `http://127.0.0.1:8001/storage/${imageName}`;
        },
        
        updateApartments(filtered) {
            console.log("Updating apartments list with:", filtered);
            this.apartments = filtered;
        },

        // Appartmenti filtrati
        handleUpdatedApartments(filteredApartments) {
            this.apartments = filteredApartments;
        }

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
                this.originalApartments = data;  // Store all data in originalApartments
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
    <HeaderApp 
    :services="services" 
    :selectedServices="selectedServices" 
    :isSidebarVisible="isSidebarVisible"
    :isSearchClicked="isSearchClicked" 
    :tempSize="tempSize" 
    :referencePoint="referencePoint"
    :distanceRange="distanceRange" 
    :search="search"

    
    :apartments="originalApartments"
    @update-apartments="handleUpdatedApartments"

    @update:search="updateSearchValue"
    @request-coordinates="getCoordinatesFromAddress"
    @search-clicked="handleSearchClick"
    @request-filter-by-distance="handleDistanceFilter"
    @update:distanceRange="tempDistanceRange => distanceRange = tempDistanceRange"
    @close-sidebar="isSidebarVisible = false"
    @toggle-sidebar="isSidebarVisible = !isSidebarVisible"
    @apply-filters="applyFilters" 
    @apartments-updated="updateApartments">
    </HeaderApp>
    <!-- @filter-by-distance="handleDistanceFilter" -->
    <div class="container-fluid">

        <!-- LISTA APPARTAMENTI -->
        <div id="apartmentList">

            <!-- Singola card -->

            <!-- Inserisco prima l'array con sponsor === 1 e poi concateno l'array con sponsor === 0 -->
            <div class="single"
                v-for="apartment in [...filteredApartments.filter(apartment => apartment.sponsor === 1), ...filteredApartments.filter(apartment => apartment.sponsor !== 1)]"
                :key="apartment.id">

                <!-- Immagine -->
                <div class="single-img">
                    <img :src="getImageUrl(apartment.cover)" alt="Apartment Image">
                </div>

                <!-- Descrizione -->
                <div class="single-body">

                    <!-- Titolo -->
                    <div class="title">
                        <h5 class="apartment-name">{{ apartment.name }}</h5>
                    </div>

                    <!-- Bottoni e Link show -->
                    <div class="show">
                        <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }">
                            <button class="visit-button button-color">
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

.button-color {
    background-color: #879ae8;
    border: none;
    color: white; // Puoi scegliere il colore del testo che preferisci
    // Aggiungi altre proprietà come necessario
}

.apartment-name {
    color: #879ae8;
}
</style>