<script>
import axios from 'axios';
import FilterSidebar from './FilterSidebar.vue';

export default {
    name: 'ApartmentHome',
    components: {
        FilterSidebar,
    },
    data() {
        return {
            apartments: [],
            services: [],

            // Barra di ricerca
            search: '',
            isSearchClicked: false,

            // Suggerimenti
            suggestions: [],
            referencePoint: null,
            selectedServices: [],
            userLocation: null,
            userCountry: null,

            // Toggle per l'offcanvas per i filtri
            showFilters: false,

            // Filtri Temporanei
            tempRooms: null,
            tempBathrooms: null,
            tempSize: null,

            // Fitri Finali
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,


        }
    },
    methods: {

        // Calcoli Raggio
        haversineDistance(coords1, coords2) {
            function toRad(value) {
                return value * Math.PI / 180;
            }

            const R = 6371; // raggio della Terra in km
            const dLat = toRad(coords2.lat - coords1.lat);
            const dLon = toRad(coords2.lng - coords1.lng);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(coords1.lat)) * Math.cos(toRad(coords2.lat)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = R * c;

            return d;
        },

        toggleFilters() {
            // Mostra o nascondi l'offcanvas dei filtri quando il pulsante "Filtri" viene cliccato
            this.showFilters = !this.showFilters;
        },

        // Applica i filtri per poi passarli al filtraggio in filterByRoomsBathroomsSize
        applyFilters(selectedServices) {
            try {
                this.selectedServices = selectedServices;
                this.selectedRooms = this.tempRooms;
                this.selectedBathrooms = this.tempBathrooms;
                this.selectedSize = this.tempSize;
                this.showFilters = false;  // Chiudi l'offcanvas
            } catch (error) {
                console.error("Errore durante l'esecuzione del codice:", error);
            }
        },



        // Coordinate date dalla search
        searchApartments() {
            if (this.search === '') {
                this.isSearchClicked = false; // Imposta isSearchClicked su false al clic del pulsante
            }

            else {
                this.isSearchClicked = true; // Imposta isSearchClicked su true al clic del pulsante
            }

            // Prende coordinate del primo suggerimento
            axios.get(`https://api.tomtom.com/search/2/geocode/${this.search}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK`)
                .then(response => {
                    const position = response.data.results[0].position;
                    this.referencePoint = {
                        lat: position.lat,
                        lng: position.lon
                    };
                })
                .catch(error => {
                    console.log(error);
                });
        },

        getUserLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    axios.get(`https://api.tomtom.com/search/2/reverseGeocode/${lat},${lng}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK`)
                        .then(response => {
                            const country = response.data.addresses[0].address.country;
                            this.userCountry = country;
                        })
                        .catch(error => {
                            console.error("Error obtaining country:", error);
                        });

                }, error => {
                    console.error("Error obtaining geolocation:", error);
                });
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        },

        getSuggestions() {
            if (this.search.trim().length < 2) {
                this.suggestions = [];
                return;
            }

            let apiUrl = `https://api.tomtom.com/search/2/search/${this.search}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK&limit=5&entityType=Municipality&language=it-IT&radius=20000`;

            if (this.userCountry) {
                apiUrl += `&countrySet=${this.userCountry}`;
            }

            axios.get(apiUrl)
                .then(response => {
                    const results = response.data.results;
                    if (this.userLocation) {
                        results.sort((a, b) => {
                            const distanceA = this.haversineDistance(this.userLocation, {
                                lat: a.position.lat,
                                lng: a.position.lon
                            });
                            const distanceB = this.haversineDistance(this.userLocation, {
                                lat: b.position.lat,
                                lng: b.position.lon
                            });
                            return distanceA - distanceB;
                        });
                    }
                    this.suggestions = results.map(result => result.address.freeformAddress);
                })
                .catch(error => {
                    console.log(error);
                });
        },

        selectSuggestion(suggestion) {
            this.search = suggestion;
            this.suggestions = [];
        },

        // Filtraggio stanze,bagni e dimensioni
        filterByRoomsBathroomsSize(apartment) {
            const roomCondition = !this.selectedRooms || apartment.room === this.selectedRooms;
            const bathroomCondition = !this.selectedBathrooms || apartment.bathroom === this.selectedBathrooms;

            let upperSizeLimit;
            if (this.selectedSize === 50) {
                upperSizeLimit = this.selectedSize + 50;
            } else {
                upperSizeLimit = this.selectedSize + 100;
            }

            const sizeCondition = !this.selectedSize || (apartment.mq >= this.selectedSize && apartment.mq < upperSizeLimit);

            return roomCondition && bathroomCondition && sizeCondition;
        },


        // Filtraggio servizi
        filterByServices(apartment) {
            return this.selectedServices.every(serviceId =>
                apartment.services.some(service => service.id === serviceId)
            );
        },



    },

    // Applicazione filtri su appartamenti
    computed: {
        filteredApartments() {
            return this.apartments.filter(apartment => {
                // Controlla la distanza solo se è stato fatto un clic di ricerca
                const distanceCondition = !this.isSearchClicked || (
                    this.referencePoint &&
                    this.haversineDistance(this.referencePoint, {
                        lat: apartment.latitude,
                        lng: apartment.longitude
                    }) <= 20
                );

                return distanceCondition &&
                    this.filterByRoomsBathroomsSize(apartment) &&
                    this.filterByServices(apartment);
            });
        }

    },


    mounted() {

        this.getUserLocation();

        axios.get('http://127.0.0.1:8000/api/v1/')
            .then(response => {
                const data = response.data;
                this.apartments = data;
            })
            .catch(error => {
                console.log(error);
            }),
            axios.get('http://127.0.0.1:8000/api/v1/service')
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

<template>
    <div class="container-fluid">

        <!-- Search -->
        <div class="row justify-content-center">
            <div class="col-8 mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cerca appartamento..." aria-label="Cerca..."
                        aria-describedby="button-addon2" v-model="search" @input="getSuggestions"
                        @keyup.enter="searchApartments">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" @click="searchApartments">Search</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Suggerimenti -->
        <div v-if="suggestions.length" class="suggestions-list">
            <ul>
                <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                    {{ suggestion }}
                </li>
            </ul>
        </div>

        <!-- FILTRI -->
        <button class="btn btn-primary" @click="toggleFilters">Filtri</button>

        <!-- PAGE FILTRI -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" :class="{ show: showFilters }">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filtri</h5>
                <button type="button" class="btn-close" @click="toggleFilters" aria-label="Chiudi"></button>
            </div>


            <div class="offcanvas-body">

                <h3 id="roomBed">Stanze e Letti</h3>

                <!-- Filtro per camere da letto -->
                <div class="mb-3 row">

                    <!-- Titolo -->
                    <label class="form-label col-4">Camere da letto:</label>

                    <!-- Bottoni -->
                    <div class="btn-group col-8" role="group" aria-label="Numero di camere">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': tempRooms === null }" @click="tempRooms = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary" v-for="n in 8"
                            v-bind:class="{ 'active': tempRooms === n }" @click="tempRooms = n">{{ n }}</button>
                    </div>
                </div>

                <!-- Filtro per bagni -->
                <div class="mb-3">

                    <!-- Titolo -->
                    <label class="form-label col-4">Bagni:</label>

                    <!-- Bottoni -->
                    <div class="btn-group col-8" role="group" aria-label="Numero di bagni">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': tempBathrooms === null }"
                            @click="tempBathrooms = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary" v-for="n in 5"
                            v-bind:class="{ 'active': tempBathrooms === n }" @click="tempBathrooms = n">{{ n
                            }}</button>
                    </div>
                </div>

                <!-- Filtro per dimensione -->
                <div class="mb-3">

                    <!-- Titolo -->
                    <label class="form-label col-4">Dimensione (mq):</label>

                    <!-- Bottoni -->
                    <div class="btn-group col-8" role="group" aria-label="Dimensione">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': tempSize === null }" @click="tempSize = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary"
                            v-for="(size, index) in [50, 100, 200, 300, 400, 500]" :key="index"
                            v-bind:class="{ 'active': tempSize === size }" @click="tempSize = size">
                            {{ size }} - {{ index < [50, 100, 200, 300, 400, 500].length - 1 ? [50, 100, 200, 300, 400,
                                500][index + 1] : '' }} </button>
                    </div>
                </div>

                <!-- COMPONENTE SIDEBAR -->
                <div class="mt-5">
                    <filter-sidebar :services="services" :selectedServices="selectedServices"
                        @apply-filters="applyFilters"></filter-sidebar>
                </div>
            </div>
        </div>


        <!-- Lista degli appartamenti -->
        <h1 class="display-4 text-center text-primary">Home BoolBNB</h1>
        <div class="d-flex flex-wrap">
            <div v-for="apartment in filteredApartments" :key="apartment.id" class="col-lg-4 col-md-6 col-sm-12 mb-4"
                :style="{ 'order': apartment.sponsor ? -1 : 0 }">
                <div class="card h-100 " :class="{ 'sponsored-card': apartment.sponsor }">
                    <img :src="apartment.cover" class="card-img-top" alt="Cover dell'appartamento"
                        style="max-height: 200px;" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ apartment.name }}</h5>
                        <p class="card-text flex-grow-1">{{ apartment.description }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }"
                                class="btn btn-primary">Dettagli</router-link>
                            <!-- Aggiungi un pulsante "Consigliato" se l'appartamento ha sponsor -->
                            <button v-if="apartment.sponsor" class="btn btn-success ml-2">Consigliato</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.container-fluid {
    background-color: white;
    text-align: center;
    padding: 70px;

    // Offcanvas
    .offcanvas {
        width: 700px;

        #roomBed {
            text-align: left;
            margin-bottom: 50px;
        }
    }

    ul {
        list-style-type: none;

        li {
            a {
                text-decoration: none;
                transition: all 0.8s ease;
                color: black;

                &:hover {
                    color: yellow; // Imposta il colore dei link
                    text-decoration: underline;

                }
            }
        }
    }

    .sponsored-card {
        background-color: #ffe7e7;
        /* Colore di sfondo leggermente rosato per le cards sponsorizzate */
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .card-img-top {
        max-height: 200px;
    }

    .btn-primary {
        background-color: #007bff;
        /* Colore di sfondo blu per il pulsante Dettagli */
        color: #fff;
    }

    .btn-success {
        background-color: #28a745;
        /* Colore di sfondo verde per il pulsante Consigliato */
        color: #fff;
    }


}
</style>

