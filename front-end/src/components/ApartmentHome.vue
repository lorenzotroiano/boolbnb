<script>
import axios from 'axios';

export default {
    name: 'ApartmentHome',
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

        // Coordinate date dalla search
        searchApartments() {
            if (this.search === '') {
                this.isSearchClicked = false; // Imposta isSearchClicked su true al clic del pulsante
            }

            else {
                this.isSearchClicked = true; // Imposta isSearchClicked su true al clic del pulsante
            }

            // Prende coordinate del primo suggerimento
            axios.get(`https://api.tomtom.com/search/2/geocode/${this.search}.json?key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD`)
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

                    axios.get(`https://api.tomtom.com/search/2/reverseGeocode/${lat},${lng}.json?key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD`)
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

            let apiUrl = `https://api.tomtom.com/search/2/search/${this.search}.json?key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD&limit=3&entityType=municipality`;

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

                    this.suggestions = results.map(result => result.address.municipality);
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
            return this.apartments.filter(apartment => {
                const distanceCondition = !this.isSearchClicked || (this.referencePoint && this.haversineDistance(this.referencePoint, {
                    lat: apartment.latitude,
                    lng: apartment.longitude
                }) <= 30);
            
                return distanceCondition && this.filterByServices(apartment);
            });
        }


    },

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

<template>
    <div class="container-fluid">

        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Cerca..." aria-label="Cerca..."
                aria-describedby="button-addon2" v-model="search" @input="getSuggestions" @keyup.enter="searchApartments">
            <div v-if="suggestions.length" class="suggestions-list">
                <ul>
                    <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                        {{ suggestion }}
                    </li>
                </ul>
            </div>
            <button class="btn btn-primary" @click="searchApartments">Search</button>
        </div>

        <!-- Lista dei servizi -->
        <div v-for="service in services" class="form-check">
            <input class="form-check-input" type="checkbox" :value="service.id" v-model="selectedServices" :id="'flexCheckIndeterminate' + service.id">
            <label class="form-check-label" :for="'flexCheckIndeterminate' + service.id">
                {{ service.name }}
            </label>
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

}

.suggestions-list {
    position: absolute;
    top: 100%;
    width: 100%;
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #ccc;
    background-color: #fff;
    z-index: 1000;

    ul {
        padding: 0;
        margin: 0;
        list-style-type: none;

        li {
            padding: 10px;
            cursor: pointer;

            &:hover {
                background-color: #f5f5f5;
            }
        }
    }
}
</style>

