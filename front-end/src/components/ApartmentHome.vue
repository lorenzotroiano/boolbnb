<script>
import axios from 'axios';
import FilterSidebar from './FilterSidebar.vue';
import tt from '@tomtom-international/web-sdk-maps';
import { services } from '@tomtom-international/web-sdk-services';
import SearchBox from '@tomtom-international/web-sdk-plugin-searchbox';

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
            searchValue: '',

            // Suggerimenti
            suggestions: [],
            referencePoint: null,
            selectedServices: [],
            userLocation: null,
            userCountry: null,

            // Toggle per l'offcanvas per i filtri
            showFilters: false,
            distanceRange: 20,

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

        // SHOW OFFCANVAS
        toggleFilters() {
            this.showFilters = !this.showFilters;
        },

        // FILTRI BAGNI, STANZE, SIZE
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

        // FILTRI SERVIZI
        filterByServices(apartment) {
            return this.selectedServices.every(serviceId =>
                apartment.services.some(service => service.id === serviceId)
            );
        },

        // FILTRI DISTANZA
        filterByDistanceRange(apartment) {
            if (!this.referencePoint) return false;

            const distance = this.haversineDistance(this.referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));

            return distance <= this.distanceRange;  // Verifica solo che la distanza sia entro il raggio impostato
        },

        // APPLICA I FILTRI
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
    },

    // FILTRAGGIO ARRAY SECONDO FILTRI
    computed: {
        filteredApartments() {
            return this.apartments.filter(apartment => {
                const distanceCondition = !this.isSearchClicked || this.filterByDistanceRange(apartment);

                return distanceCondition &&
                    this.filterByRoomsBathroomsSize(apartment) &&
                    this.filterByServices(apartment);
            });
        },

    },


    // CHIAMATE AXIOS PER SERVIZI E APPARTAMENTI
    mounted() {
        const options = {
            searchOptions: {
                key: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
                language: "it-It",
                limit: 5,
            },
            autocompleteOptions: {
                key: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
                language: "it-It",

            },
        };

        // Crea un'istanza di SearchBox
        const ttSearchBox = new SearchBox(services, options);

        // Ottieni l'HTML della casella di ricerca
        const searchBoxHTML = ttSearchBox.getSearchBoxHTML();

        // Inserisci la casella di ricerca nell'elemento HTML desiderato
        this.$refs.searchBoxContainer.appendChild(searchBoxHTML);
        // this.getUserLocation();

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


<!-- TEMPLATE -->
<template>
    <div class="container-fluid">
        <div ref="searchBoxContainer"></div>
        <p>Risultato ricerca: {{ searchValue }}</p>
        <!-- SEARCH -->
        <div class="row justify-content-center flex-column align-content-center">

            <!-- Ricerca -->
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

            <!-- SUGGERIMENTI -->
            <div class="col-2" id="suggest">
                <div v-if="suggestions.length" class="suggestions-list d-flex justify-content-center">
                    <ul>
                        <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                            {{ suggestion }}
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Filtri distanza -->
        <div class="mb-3">
            <label for="distanceRange" class="form-label">Distanza (km)</label>
            <input type="range" class="form-range" id="distanceRange" min="0" max="100" step="1" v-model="distanceRange">
            <div>{{ distanceRange }} km</div>
        </div>

        <!-- FILTRI -->
        <button class="btn btn-primary" @click="toggleFilters">Filtri</button>

        <!-- Page Filtri -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" :class="{ show: showFilters }">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filtri</h5>
                <button type="button" class="btn-close" @click="toggleFilters" aria-label="Chiudi"></button>
            </div>


            <div class="offcanvas-body">

                <h3>Stanze e Letti</h3>

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
                <div class="mb-3 row">

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
                <div class="mb-3 row">

                    <!-- Titolo -->
                    <label class="form-label col-4">Dimensione (mq):</label>

                    <!-- Bottoni -->
                    <div class="btn-group col-8" role="group" aria-label="Dimensione">
                        <button type="button" class="btn btn-outline-secondary"
                            v-bind:class="{ 'active': tempSize === null }" @click="tempSize = null">Qualsiasi</button>
                        <button type="button" class="btn btn-outline-secondary"
                            v-for="(size, index) in [50, 100, 200, 300, 400, 500]" :key="index"
                            v-bind:class="{ 'active': tempSize === size }" @click="tempSize = size">
                            {{ size }} </button>
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

    #suggest {
        margin: 0 auto; // Centra il div orizzontalmente


        // Suggerimenti
        .suggestions-list {
            border: 1px solid #e0e0e0;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;

            ul {
                width: 100%;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            li {
                padding: 10px 15px;
                border-bottom: 1px solid #f1f1f1;
                cursor: pointer;
                transition: background-color 0.2s ease;

                &:hover {
                    background-color: #f5f5f5;
                }

                &:active {
                    background-color: #e0e0e0;
                }

                &:last-child {
                    border-bottom: none;
                }
            }
        }
    }


    // Offcanvas
    .offcanvas {
        width: 900px;

        h3 {
            text-align: left;
            margin-bottom: 50px;
        }

        .btn {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            transition: all 0.3s ease-in-out;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            /* padding uniforme per centrare il testo */
            font-weight: 600;
            border-width: 1px;

            &:not(.active):hover {
                transform: scale(1.05);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }

            &.active {
                background-color: #007BFF;
                color: white;
                border-color: #0056b3;
            }

            &:not(.active) {
                background-color: white;
                color: #333;

                &:hover {
                    background-color: #f8f9fa;
                    border-color: #dae0e5;
                }
            }
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
