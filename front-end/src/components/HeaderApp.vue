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
                    <a href="http://localhost:5173"><img src="../assets/img/logoBoolbnb.png" alt=" Logo"></a>
                </div>

                <!-- Logo -->
                <div class="logo-mobile">
                    <a href="http://localhost:5173"><img src="../assets/img/logoBoolbnb-mobile.png" alt="Logo"></a>
                </div>

                <!-- Searchbar -->
                <div class="search">
                    <div class="d-flex w-50">
                        <input type="text" placeholder="Cerca" :value="search" @input="updateSearch" @keyup.enter="onSearch"
                            @focus="handleSearchClick" @blur="hideSuggestions">
                        <!-- Suggerimenti -->
                        <ul v-if="suggestions.length">
                            <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                                {{ suggestion }}
                            </li>
                        </ul>
                        <button class="me-3" @click="onSearch">
                            <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                        </button>
                        <button style="width: 70px;" class="me-3" @click="$emit('toggle-sidebar')">Filtri</button>
                    </div>

                    <div class="d-flex w-50 align-items-center">
                        <!-- Distanza -->
                        <div class=" d-flex align-items-center w-100">
                            <span class="d-block me-3">{{ distanceRange }} km</span>
                            <input type="range" class="form-range custom-range" v-model="distanceRange"
                                @change="handleSliderChange" />

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
            <!-- COMPONENTE SIDEBAR -->

            <transition name="slide">
                <FilterSidebar v-show="isSidebarVisible" :services="services" :selectedServices="selectedServices"
                    :isSidebarVisible="isSidebarVisible" :isSearchClicked="isSearchClicked" :tempSize="tempSize"
                    :referencePoint="referencePoint" :distanceRange="distanceRange" :apartments="apartments"
                    @filter-by-distance="$emit('filter-by-distance', $event)"
                    @update:distanceRange="$emit('update:distanceRange', $event)" @close-sidebar="$emit('close-sidebar')"
                    @apply-filters="$emit('apply-filters')" @apartments-updated="$emit('apartments-updated', $event)">
                </FilterSidebar>
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
                position: relative;

                &:hover {
                    box-shadow: 0 0 2px rgba(0, 0, 0, 0.6);
                }

                ul {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    background-color: white;
                    border: 1px solid #ccc;
                    width: 100%;
                    list-style-type: none;
                    padding: 0;
                    z-index: 10;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

                    li {
                        padding: 10px;
                        cursor: pointer;
                        border-bottom: 1px solid #eee;

                        &:last-child {
                            border-bottom: none;
                        }

                        &:hover {
                            background-color: #f5f5f5;
                        }
                    }
                }
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

                    a {
                        text-decoration: none;
                        color: $color-blue-hover;
                    }
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

// Login e registrati
.button-52 {
    font-size: 16px;
    font-weight: 200;
    letter-spacing: 1px;
    padding: 13px 20px 13px;
    outline: 0;
    border: 1px solid rgba(122, 122, 122, 0.404);
    cursor: pointer;
    position: relative;
    background-color: rgba(0, 0, 0, 0);
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    color: white;
    text-decoration: none;
}

.button-52:after {
    content: "";
    background-color: $color-dark-purple;
    width: 100%;
    z-index: -1;
    position: absolute;
    height: 100%;
    top: 7px;
    left: 7px;
    transition: 0.2s;
}

.button-52:hover:after {
    top: 0px;
    left: 0px;
}

@media (min-width: 768px) {
    .button-52 {
        padding: 13px 50px 13px;
    }
}
</style>
