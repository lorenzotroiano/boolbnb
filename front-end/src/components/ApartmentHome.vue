<script>
import axios from 'axios';

export default {
    name: 'ApartmentHome',
    data() {
        return {
            apartments: [],
            search: '',
            referencePoint: null,
            isSearchClicked: false,
            suggestions: []
        }
    },
    methods: {
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
        searchApartments() {
            if (this.search === '') {
                this.isSearchClicked = false; // Imposta isSearchClicked su true al clic del pulsante
            }

            else {
                this.isSearchClicked = true; // Imposta isSearchClicked su true al clic del pulsante
            }

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
        getSuggestions() {
        if (this.search.trim() === '') {
            this.suggestions = [];
            return;
        }

        axios.get(`https://api.tomtom.com/search/2/search/${this.search}.json?key=2hSUhlhHixpowSvWwlyl6oARrDT01OsD&limit=5`)
            .then(response => {
                this.suggestions = response.data.results.map(result => result.address.freeformAddress);
            })
            .catch(error => {
                console.log(error);
            });
        },
        
        selectSuggestion(suggestion) {
        this.search = suggestion;
        this.suggestions = [];
        }
    },
    computed: {
        filteredApartments() {
            if (!this.isSearchClicked) {
                return this.apartments; // Mostra tutti gli appartamenti se non è stato fatto clic su "Search"
            }

            return this.apartments.filter(apartment => {
                const distance = this.haversineDistance(this.referencePoint, {
                    lat: apartment.latitude,
                    lng: apartment.longitude
                });
                return distance <= 100;
            });
        }
    },

    mounted() {
        axios.get('http://127.0.0.1:8001/api/v1/')
            .then(response => {
                const data = response.data;
                this.apartments = data;
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
                aria-describedby="button-addon2" v-model="search" @input="getSuggestions">
            <div v-if="suggestions.length" class="suggestions-list">
                <ul>
                    <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                        {{ suggestion }}
                    </li>
                </ul>
            </div>
            <button class="btn btn-primary" @click="searchApartments">Search</button>
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

