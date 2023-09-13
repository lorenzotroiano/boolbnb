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
            showFilters: false
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
                // Applica i filtri quando l'utente fa clic su "Applica filtri" nell'offcanvas
                this.selectedServices = selectedServices;
                // Chiudi l'offcanvas
                this.showFilters = false;
            } catch (error) {
                console.error("Errore durante l'esecuzione del codice:", error);
            }
            console.log(selectedServices);
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

            let apiUrl = `https://api.tomtom.com/search/2/search/${this.search}.json?key=ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK&limit=5&entityType=Municipality&countrySet=IT&language=it-IT&radius=20000`;

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

        filterByServices(apartment) {
            return this.selectedServices.every(serviceId =>
                apartment.services.some(service => service.id === serviceId)
            );
        }

    },
    computed: {

        // Lista di appartamenti filtrata
        filteredApartments() {
            if (!this.isSearchClicked && this.selectedServices.length === 0) {
                return this.apartments;
            }

            return this.apartments.filter(apartment => {
                const distanceCondition = !this.isSearchClicked || (this.referencePoint && this.haversineDistance(this.referencePoint, {
                    lat: apartment.latitude,
                    lng: apartment.longitude
                }) <= 20);

                return distanceCondition && this.filterByServices(apartment);
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

        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Cerca..." aria-label="Cerca..."
                aria-describedby="button-addon2" v-model="search" @input="getSuggestions" @keyup.enter="searchApartments">
            <button class="btn btn-primary" @click="searchApartments">Search</button>
        </div>
        <div v-if="suggestions.length" class="suggestions-list">
            <ul>
                <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                    {{ suggestion }}
                </li>
            </ul>
        </div>

        <button class="btn btn-primary" @click="toggleFilters">Filtri</button>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" :class="{ show: showFilters }">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filtri</h5>
                <button type="button" class="btn-close" @click="toggleFilters" aria-label="Chiudi"></button>
            </div>
            <div class="offcanvas-body">
                <filter-sidebar :services="services" :selectedServices="selectedServices"
                    @apply-filters="applyFilters"></filter-sidebar>
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

