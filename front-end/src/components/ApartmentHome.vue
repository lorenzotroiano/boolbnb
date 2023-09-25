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

            // Posizione slide
            activeItem: 0

        }
    },
    methods: {

        // Prendi l'url dell'immagine
        getImageUrl(imageName) {
            console.log(imageName);
            return `http://127.0.0.1:8000/storage/${imageName}`;
        },

        updateApartments(filtered) {
            console.log("Updating apartments list with:", filtered);
            this.apartments = filtered;
        },

        // Appartmenti filtrati
        handleUpdatedApartments(filteredApartments) {
            this.apartments = filteredApartments;
        },

        // Appartmenti filtrati per sponsor
        sponsorFilteredApartments() {
            return this.apartments.filter(apartment => apartment.sponsor === 1);
        },

        // Comandi per carosello
        changeActive(index) {
            this.activeItem = index;
        },

        nextSlide() {
            if (this.activeItem < this.sponsorFilteredApartments().length - 1) {
                this.activeItem++;
            } else {
                this.activeItem = 0; // torna al primo slide
            }
        },

        prevSlide() {
            if (this.activeItem > 0) {
                this.activeItem--;
            } else {
                this.activeItem = this.sponsorFilteredApartments().length - 1; // torna all'ultimo slide
            }
        }

    },

    // FILTRAGGIO ARRAY SECONDO FILTRI
    computed: {
        filteredApartments() {
            return this.apartments.filter(apartment => {
                // Aggiungi filtro per servizi
                const servicesCondition = this.selectedServices.every(service => apartment.services.includes(service));

                // Condizione per filtrare per numero di stanze
                const roomCondition = !this.selectedRooms || apartment.rooms === this.selectedRooms;

                // Condizione per filtrare per numero di bagni
                const bathroomCondition = !this.selectedBathrooms || apartment.bathrooms === this.selectedBathrooms;

                // Condizione per filtrare per metri quadrati
                const sizeCondition = !this.tempSize || apartment.size >= this.tempSize;

                return servicesCondition && bathroomCondition && sizeCondition && roomCondition;

                // Il filtro per distanza è completamente gestito in HeaderApp
            });
        },
    },


    // CHIAMATE AXIOS PER SERVIZI E APPARTAMENTI
    mounted() {
        axios.get('http://127.0.0.1:8000/api/v1/')
            .then(response => {
                const data = response.data;
                this.originalApartments = data;  // Store all data in originalApartments
                this.apartments = data;
            })
            .catch(error => {
                console.log(error);
            });

        axios.get('http://127.0.0.1:8000/api/v1/service')
            .then(response => {
                const data = response.data;
                this.services = data;
            })
            .catch(error => {
                console.log(error);
            });
    },

};
</script>


<!-- TEMPLATE -->
<template>
    <HeaderApp :services="services" :selectedServices="selectedServices" :isSidebarVisible="isSidebarVisible"
        :tempSize="tempSize" :referencePoint="referencePoint" :apartments="originalApartments"
        @update-apartments="handleUpdatedApartments"
        @update:distanceRange="tempDistanceRange => distanceRange = tempDistanceRange"
        @close-sidebar="isSidebarVisible = false" @toggle-sidebar="isSidebarVisible = !isSidebarVisible"
        @apply-filters="applyFilters" @apartments-updated="updateApartments">
    </HeaderApp>
    <!-- @filter-by-distance="handleDistanceFilter" -->
    <div class="container-fluid">

        <!-- Carosello -->
        <div class="carousel">
            <div class="image-container"
                :style="{ display: this.originalApartments === this.apartments ? 'flex' : 'none' }">
                <div v-for="(apartment, index) in sponsorFilteredApartments()" :key="index" class="carousel-item"
                    :class="{ active: activeItem === index }" @click="changeActive(index)">
                    <img :src="getImageUrl(apartment.cover)" alt="Immagine app sponsor">
                    <div class="description">
                        <h3>{{ apartment.name }}</h3>
                        <p>{{ apartment.address }}</p>
                    </div>
                </div>
                <!-- Inserisco il bottone per il metodo "indietro"-->
                <button id="prev" @click="prevSlide" type="button"><i class="fa-solid fa-arrow-left"></i></button>

                <!-- Inserisco il bottone per il metodo "avanti"  -->
                <button id="next" @click="nextSlide" type="button"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>




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
                                Esplora
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
