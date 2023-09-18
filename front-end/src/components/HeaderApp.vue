<script>
import FilterSidebar from './FilterSidebar.vue';
import { haversineDistance } from '../../utils';

export default {
    name: "HeaderApp",
    components: {
        FilterSidebar
    },
    props: [
        'services',
        'selectedServices',
        'referencePoint',
        'distanceRange',
        'isSidebarVisible',
        'apartments',
        'isSearchClicked',
        'tempSize',
        'search'
    ],
    data() {
        return {

        }

    },
    methods: {

        onSearch() {
            console.log("onSearch called with value:", this.search);
            this.$emit('request-coordinates', this.search);
            this.$emit('update:isSearchClicked', true); // Emette un evento quando viene eseguita una ricerca
        },

        updateSearch(event) {
            this.$emit('update:search', event.target.value);
        },

       
        // FILTRI DISTANZA
        filterByDistanceRange(apartment) {
            if (!this.referencePoint) return false;

            const distance = haversineDistance(this.referencePoint, new tt.LngLat(apartment.longitude, apartment.latitude));

            return distance <= this.distanceRange;  // Verifica solo che la distanza sia entro il raggio impostato
        },

        handleDistanceFilter() {
            const filteredApartments = this.apartments.filter(this.filterByDistanceRange);
            this.updateApartments(filteredApartments);
        },

        filterApartmentsByDistance() {
            const filteredApartments = this.apartments.filter(this.filterByDistanceRange);
            this.$emit('apartments-updated', filteredApartments);
        },
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

                <!-- Logo -->
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
        <!-- COMPONENTE SIDEBAR -->

        <transition name="slide">
            <FilterSidebar v-show="isSidebarVisible" :services="services" :selectedServices="selectedServices"
                :isSidebarVisible="isSidebarVisible" :isSearchClicked="isSearchClicked" :tempSize="tempSize"
                :referencePoint="referencePoint" :distanceRange="distanceRange" :apartments="apartments"

                @filter-by-distance="$emit('filter-by-distance', $event)"
                @update:distanceRange="$emit('update:distanceRange', $event)"
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
