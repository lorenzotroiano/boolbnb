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
            search: '',
            referencePoint: null,
            isSearchClicked: false,
            suggestions: [],
            selectedServices: [],
            userLocation: null,
            userCountry: null,
            showFilters: false,

            // Fitri ulteriori
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,

            // Range filtri
            tempRooms: null,
            tempBathrooms: null,
            tempSize: null

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
        applyFilters(selectedServices) {
            try {
                this.selectedServices = selectedServices;
                this.selectedRooms = this.tempRooms;
                this.selectedBathrooms = this.tempBathrooms;
                this.selectedSize = this.tempSize;
                // Chiudi l'offcanvas
                this.showFilters = false;
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

            let apiUrl = `https://api.tomtom.com/search/2/search/${this.search}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK&limit=5&entityType=municipality`;

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
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Cerca..." aria-label="Cerca..."
                aria-describedby="button-addon2" v-model="search" @input="getSuggestions" @keyup.enter="searchApartments">
            <button class="btn btn-primary" @click="searchApartments">Search</button>
        </div>

        <!-- Suggerimenti -->
        <div v-if="suggestions.length" class="suggestions-list">
            <ul>
                <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                    {{ suggestion }}
                </li>
            </ul>
        </div>

        <!-- Filtri -->
        <button class="btn btn-primary" @click="toggleFilters">Filtri</button>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" :class="{ show: showFilters }">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filtri</h5>
                <button type="button" class="btn-close" @click="toggleFilters" aria-label="Chiudi"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Filtro per camere da letto -->
                <div class="mb-3">
                    <label class="form-label">Camere da letto:</label>
                    <div class="btn-group" role="group" aria-label="Numero di camere">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': selectedRooms === null }"
                            @click="selectedRooms = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary" v-for="n in 8"
                            v-bind:class="{ 'active': selectedRooms === n }" @click="selectedRooms = n">{{ n }}</button>
                    </div>
                </div>

                <!-- Filtro per bagni -->
                <div class="mb-3">
                    <label class="form-label">Bagni:</label>
                    <div class="btn-group" role="group" aria-label="Numero di bagni">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': selectedBathrooms === null }"
                            @click="selectedBathrooms = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary" v-for="n in 5"
                            v-bind:class="{ 'active': selectedBathrooms === n }" @click="selectedBathrooms = n">{{ n
                            }}</button>
                    </div>
                </div>

                <!-- Filtro per dimensione -->
                <div class="mb-3">
                    <label class="form-label">Dimensione (mq):</label>
                    <div class="btn-group" role="group" aria-label="Dimensione">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': selectedSize === null }"
                            @click="selectedSize = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary"
                            v-for="(size, index) in [50, 100, 200, 300, 400, 500]" :key="index"
                            v-bind:class="{ 'active': selectedSize === size }" @click="selectedSize = size">
                            {{ size }} - {{ index < [50, 100, 200, 300, 400, 500].length - 1 ? [50, 100, 200, 300, 400,
                                500][index + 1] : '' }} </button>

                    </div>
                </div>


                <filter-sidebar :services="services" :selectedServices="selectedServices"
                    @apply-filters="applyFilters"></filter-sidebar>
            </div>
        </div>


        <!-- Lista degli appartamenti -->
        <h1 class="display-4 text-center text-primary">Home BoolBNB</h1>
        <div class="row">
            <div class="col-sm-4 my-3" v-for="apartment in filteredApartments" :key="apartment.id">
                <div class="card shadow bg-info ">
                    <div class="card-body">
                        <ul>
                            <li>
                                <router-link :to="{ name: 'apartment-show', params: { id: apartment.id } }" class="link">
                                    <h2 class="card-title">{{ apartment.name }}</h2>
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>


    </div>
</template>

<style lang="scss">
.container-fluid {

    background-color: orange;
    text-align: center;
    padding: 70px;

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

    .offcanvas {
        width: 500px;
    }
}
</style>

