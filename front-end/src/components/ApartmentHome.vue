<script>
import axios from 'axios';
import FilterSidebar from './FilterSidebar.vue';
import tt from '@tomtom-international/web-sdk-maps';

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

        // SUGGERIMENTI CITTÀ
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
        this.getUserLocation();

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
    <div class="container-fluid">

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
                        <button @click="isSidebarVisible = !isSidebarVisible">Filtri avanzati</button>
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

        

        <!-- COMPONENTE SIDEBAR -->
        <div class="mt-5">

            <transition name="slide">
                <FilterSidebar
                v-show="isSidebarVisible"
                :services="services"
                :selectedServices="selectedServices"
                :isSidebarVisible="isSidebarVisible"


                :referencePoint="referencePoint"
                :distanceRange="distanceRange"

                
                @update:distanceRange="value => distanceRange = value"

                @close-sidebar="isSidebarVisible = false"
                @filter-by-distance="handleDistanceFilter"
                @apply-filters="applyFilters"
                @apartments-updated="updateApartments"
                ></FilterSidebar>
            </transition>
        
        </div>
        
        <!-- Overlay quando SIDEBAR è TRUE -->
        <div v-if="isSidebarVisible" class="overlay" @click="isSidebarVisible = false"></div>

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


    // // CSS PER IL COMPONENTE SIDEBAR
    // .slide-enter {
    //     transform: translateY(100%); 
    // }

    // /* Stile finale dell'animazione di entrata */
    // .slide-enter-to, .slide-enter-active {
    //     transform: translateY(-50%);
    // }

    // /* Stile iniziale dell'animazione di uscita */
    // .slide-leave {
    //     transform: translateY(-50%);
    // }

    // /* Stile finale dell'animazione di uscita */
    // .slide-leave-to, .slide-leave-active {
    //     transform: translateY(100%);
    // }

    /* overlay style */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(0,0,0,0.5); /* semi-transparent */
        z-index: 999; /* dietro la sidebar ma al di sopra di tutto il resto */
    }


}
</style>

