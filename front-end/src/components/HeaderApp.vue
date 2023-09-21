<script>
import axios from 'axios';

import FilterSidebar from './FilterSidebar.vue';

import { services } from '@tomtom-international/web-sdk-services';
import tt from '@tomtom-international/web-sdk-maps';

import { haversineDistance } from '../../utils';
import { levenshtein } from '../../utils';

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
        'tempSize',
    ],
    data() {
        return {
            distanceRange: 50,
            referencePoint: null,
            search: '',

            isSearchClicked: Boolean,
            // Dati da mandare a FilterSidebar per filtrare sugli appartamenti
            // che escono dalla ricerca per distanza
            apartmentsInRange: [],

            // Suggerimenti
            suggestions: [],
            // Timeout per il blur
            blurTimeout: null
        };
    },
    methods: {

        // SEARCH METHODS************************************************************
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
            if (this.search.length >= 1) {  // Suggerisci solo se ci sono almeno 3 caratteri
                this.fetchSuggestions(this.search);
            } else {
                this.suggestions = [];  // Pulisci i suggerimenti
            }
        },

        // FILTERING BY DISTANCE METHODS**********************************************
        handleSliderChange() {
            this.filterByDistanceRange();
        },

        filterByDistanceRange() {
            if (!this.referencePoint || !this.apartments) return;

            this.apartmentsInRange = this.apartments.filter(apartment => {
                const distance = haversineDistance(this.referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));
                return distance <= this.distanceRange;
            });

            this.$emit('apartments-updated', this.apartmentsInRange);
        },

        // SUGGESTIONS METHODS**************************

        async fetchSuggestions(query) {
            const requestUrl = `https://api.tomtom.com/search/2/search/${query}.json?typeahead=true&limit=200&sortBy=relevance&categorySet=7315&countrySet=IT&language=it-IT&minFuzzyLevel=2&maxFuzzyLevel=3&key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD`;
            try {
                const response = await axios.get(requestUrl);
                if (response.data && response.data.results) {
                    this.suggestions = response.data.results.map(result => {
                        let suggestionText = result.address.municipality || result.address.freeformAddress;
                        if (result.address.administrativeAreaLevel2) {
                            suggestionText += `, ${result.address.administrativeAreaLevel2}`;
                        }
                        if (result.address.country) {
                            suggestionText += `, ${result.address.country}`;
                        }
                        return suggestionText;
                    });

                    // Remove duplicates
                    this.suggestions = [...new Set(this.suggestions)];

                    // Score each suggestion
                    const scoreSuggestion = (suggestion) => {
                        let score = 0;

                        // Exact match
                        if (suggestion.toLowerCase() === query.toLowerCase()) {
                            score += 1000;  // Assigning a high score for exact matches
                        }

                        // Use Levenshtein distance for similarity
                        score -= levenshtein(query, suggestion);  // Subtracting to make closer matches have higher scores

                        return score;
                    }

                    // Sort based on scores
                    this.suggestions.sort((a, b) => scoreSuggestion(b) - scoreSuggestion(a));  // Higher scores come first

                    // Optionally, you can limit the suggestions again to the top 3 matches.
                    this.suggestions = this.suggestions.slice(0, 3);
                }
            } catch (error) {
                console.error("Error fetching suggestions:", error);
            }
        },

        selectSuggestion(suggestion) {
            clearTimeout(this.blurTimeout);
            this.search = suggestion;
            this.suggestions = [];
            this.onSearch();
        },


        // FETCH COORDINATES FROM THE ADDRESS THE USER INPUTS IN
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

        // Controlla se clicco la searchbar
        handleSearchClick() {
            this.isSearchClicked = true;
        },
        hideSuggestions() {
            this.blurTimeout = setTimeout(() => {
                this.suggestions = [];
            }, 150);  // ritardo di 200ms
        }
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
            <div id="bar" class="flex align-items-center">

                <!-- Logo immagine -->
                <div class="logo">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb.png" alt=" Logo"></a>
                </div>

                <!-- Logo mobile -->
                <div class="logo-mobile">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb-mobile.png" alt="Logo"></a>
                </div>

                <div id="find">
                    <!-- Distanza -->
                    <div class="range-bar">
                        <div class="testo-range">In un raggio di <span>{{ distanceRange }} km</span></div>
                        <input type="range" class="form-range custom-range" v-model="distanceRange"
                            @change="handleSliderChange" />
                    </div>

                    <!-- Searchbar -->
                    <div class="search">
                        <div class="d-flex">
                            <input type="text" placeholder="Cerca" :value="search" @input="updateSearch"
                                @keyup.enter="onSearch" @focus="handleSearchClick" @blur="hideSuggestions">
                            <!-- Suggerimenti -->
                            <ul v-if="suggestions.length">
                                <li v-for="suggestion in suggestions" :key="suggestion"
                                    @click="selectSuggestion(suggestion)">
                                    {{ suggestion }}
                                </li>
                            </ul>
                            <button class="me-3" @click="onSearch">
                                <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                            </button>
                            <button style="width: 70px;" class="me-3" @click="$emit('toggle-sidebar')">Filtri</button>
                        </div>
                    </div>
                </div>



                <!-- Nav -->
                <nav>
                    <ul class="menu">
                        <li><a class="button-52" href="http://127.0.0.1:8000/login">Login</a> </li>
                        <li><a class="button-52" href="http://127.0.0.1:8000/register">Registrati</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="spacer" ref="spacer">
            <transition name="slide">
                <FilterSidebar v-show="isSidebarVisible" :services="services" :selectedServices="selectedServices"
                    :isSidebarVisible="isSidebarVisible" :isSearchClicked="isSearchClicked" :tempSize="tempSize"
                    :apartments="referencePoint ? apartmentsInRange : apartments" @close-sidebar="$emit('close-sidebar')"
                    @apply-filters="$emit('apply-filters')" @apartments-updated="$emit('apartments-updated', $event)">
                </FilterSidebar>
            </transition>
        </div>

        <!-- Overlay quando SIDEBAR è TRUE -->
        <div v-if="isSidebarVisible" class="overlay" @click="isSidebarVisible = false"></div>
    </header>
</template>


<style lang="scss" scoped>
@use '../styles/general.scss';
@use '../styles/partials/_header.scss';
@use "../styles/partials/_variables" as *;
</style>
