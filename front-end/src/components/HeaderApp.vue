<script>
import axios from 'axios';

import FilterSidebar from './FilterSidebar.vue';

import { services } from '@tomtom-international/web-sdk-services';
import tt from '@tomtom-international/web-sdk-maps';

import { haversineDistance } from '../../utils';

export default {
    name: "HeaderApp",
    components: {
        FilterSidebar
    },
    props: [
        'services',
        'selectedServices',
        'isSidebarVisible',
        'apartments',
        'isSearchClicked',
        'tempSize',
    ],
    data() {
        return {
            distanceRange: 50,
            referencePoint: null,
            search: '',

            // Dati da mandare a FilterSidebar per filtrare sugli appartamenti
            // che escono dalla ricerca per distanza
            apartmentsInRange: []
        };
    },
    methods: {
        onSearch() {
            if (!this.search.trim()) {
                console.warn("Search value is empty, enter a city");
                return;
            }
            console.log("onSearch called with value:", this.search);
            this.getCoordinatesFromAddress(this.search);
        },
       
        updateSearch(event) {
            this.search = event.target.value;
        },

        handleSliderChange() {
            this.filterByDistanceRange();
        },

        filterByDistanceRange() {
            if (!this.referencePoint || !this.apartments) return;

            const filteredApartments = this.apartments.filter(apartment => {
                const distance = haversineDistance(this.referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));
                return distance <= this.distanceRange;
            });

            this.$emit('update-apartments', filteredApartments);
        },
        
        async getCoordinatesFromAddress(address) {
            console.log("Received request to get coordinates for address:", address);
            
            const requestUrl = `https://api.tomtom.com/search/2/geocode/${address}.json?key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD`;

            console.log("Sending Request:", requestUrl);
            try {
                const response = await axios.get(requestUrl);
                console.log("Coordinates received for", address, ":", response.data);

                if (response.data && response.data.results && response.data.results.length) {
                    const result = response.data.results[0];
                    this.referencePoint = {
                        lat: result.position.lat,
                        lon: result.position.lon
                    };
                    this.filterByDistanceRange();  // Chiamiamo la funzione corretta qui
                    console.log("Reference point set:", this.referencePoint);
                } else {
                    console.warn("No results found for address:", address);
                }
            } catch (error) {
                console.error("Error fetching coordinates:", error);
            }
        },

        handleSearchClick() {
            this.isSearchClicked = true;
        }, 
    },
    watch: {
        search(newSearch) {
            this.search = newSearch;  // Aggiorniamo search quando search cambia
        }
    }
}
</script>

<template>
    <header>
        <div class="container">
            <div class="flex">

                <!-- Logo immagine -->
                <div class="logo">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb.png" alt=" Logo"></a>
                </div>

                <!-- Logo mobile -->
                <div class="logo-mobile">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb-mobile.png" alt="Logo"></a>
                </div>

                <!-- Searchbar -->
                <div class="search">
                    <input type="text" placeholder="Vai ovunque" :value="search" @input="updateSearch">
                    <button @click="onSearch">
                        <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                    </button>
                    <button @click="$emit('toggle-sidebar')">Filtri</button>
                </div>
                <!-- Distanza -->
                <div class="mb-3">
                        <label for="distanceRange" class="form-label">Distanza (km)</label>
                        <input 
                            type="range" 
                            class="form-range custom-range" 
                            v-model="distanceRange" 
                            @change="handleSliderChange" />
                        <div>{{ distanceRange }} km</div>
                    </div>

                <!-- Nav -->
                <nav>
                    <ul class="menu">
                        <li><a href="http://127.0.0.1:8001/login">Login</a> </li>
                        <li><a href="http://127.0.0.1:8001/register">Registrati</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="spacer" ref="spacer">
            <transition name="slide">
                <FilterSidebar 
                    v-show="isSidebarVisible" 
                    :services="services" 
                    :selectedServices="selectedServices"
                    :isSidebarVisible="isSidebarVisible" 
                    :isSearchClicked="isSearchClicked" 
                    :tempSize="tempSize"
                    
                    :apartments="apartments"
                    
    
                    @close-sidebar="$emit('close-sidebar')"
                    @apply-filters="$emit('apply-filters')"
                    @apartments-updated="$emit('apartments-updated', $event)"
                ></FilterSidebar>
            </transition>
        </div>

        <!-- Overlay quando SIDEBAR è TRUE -->
        <div v-if="isSidebarVisible" class="overlay" @click="isSidebarVisible = false"></div>
    </header>
</template>


<style lang="scss" scoped>
@use "../styles/partials/variables" as *;

header {
    position: fixed;
    width: 100%;
    height: 100px;
    background-color: white;
    z-index: 999;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;

    .container {
        max-width: 80%;
        margin: 0 auto;

        .flex {
            display: flex;
            align-items: center;
            justify-content: space-between;

            .logo {
                width: 180px;

                img {
                    max-width: 100%;
                    max-height: 100%;
                    border-radius: 18px;
                }
            }

            @media screen and (min-width: 1200px) {
                .logo-mobile {
                    display: none;
                }
            }

            .search {
                background-color: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border-radius: 18px;
                padding: 8px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 38%;
                margin: 20px auto;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
                cursor: pointer;
            }

            .search:hover {
                transition: transform 0.2s ease-in-out;
            }

            input {
                border: none;
                padding: 4px;
                border-radius: 20px;
                width: 80%;
                outline: none;

            }

            button {
                background-color: $color-blue-hover;
                color: #fff;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 30px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: $color-dark-purple;
            }

            .menu {
                display: flex;
                align-items: center;
                width: 40%;

                li {
                    margin: 0 12px;
                }
            }
        }
    }
}
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999; 
}

@media screen and (max-width:1200px) {
    .logo {
        display: none;
    }

    .logo-mobile {
        width: 60px;
        display: block;

        img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 80px;
        }
    }
}
</style>
