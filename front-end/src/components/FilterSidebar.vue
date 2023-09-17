<script>
export default {
    props: [
        'services',
        'selectedServices',
        'referencePoint',
        'distanceRange',
        'isSidebarVisible',
        'apartments',
        'isSearchClicked',
        'tempSize',
    ],
    data() {
        return {
            selectedServicesCopy: [...this.selectedServices],
            filteredApartments: [],
            
            counterFilter: null,
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,

            tempDistanceRange: this.distanceRange,
        };
    },
    methods: {

        // RESET OF ROOMS
        resetRoomFilter() {
            this.selectedRooms = null;
            this.updateCounter();
        },

        // RESET OF BATHROOMS
        resetBathroomFilter() {
            this.selectedBathrooms = null;
            this.updateCounter();
        },

        // RESETS ALL FILTERS
        resetAllFilters() {
            this.selectedRooms = null;
            this.selectedBathrooms = null;
            this.selectedSize = null;
            this.selectedServicesCopy = [];
            this.updateCounter();
        },

        // INIZIALIZZA I FILTRI RESETTANDO E FACENDO IL CONTEGGIO
        initializeFilters() {
            this.resetAllFilters();
            this.updateCounter();
        },

        // UPDATE DEL COUNTER DEGLI APPARTAMENTI IN "MOSTRA X APPARTAMENTI"
        updateCounter() {
            const filteredApartments = this.apartments.filter((apartment) => {
                return (
                    this.filterByRooms(apartment) &&
                    this.filterByBathrooms(apartment) &&
                    this.filterBySize(apartment) &&
                    this.filterByServices(apartment)
                );
            });

            this.counterFilter = filteredApartments.length;
        },

        // LOGICA DI FILTRAGGIO********************************************************
        // STANZE
        filterByRooms(apartment) {
            if (parseInt(this.selectedRooms) === 9) {
                return apartment.room > 8;
            }
            return !this.selectedRooms || apartment.room === this.selectedRooms;
        },

        // BAGNI
        filterByBathrooms(apartment) {
            return !this.selectedBathrooms || apartment.bathroom === this.selectedBathrooms;
        },

        // METRI QUADRATI
        filterBySize(apartment) {
            return this.selectedSize === null || (apartment.mq <= this.selectedSize);
        },

        // SERVIZI
        filterByServices(apartment) {
            if (!this.selectedServicesCopy.length) return true;
            return this.selectedServicesCopy.some(serviceId => apartment.services.includes(serviceId));
        },

        updateSelectedServices(serviceId) {
            if (this.selectedServicesCopy.includes(serviceId)) {
                this.selectedServicesCopy = this.selectedServicesCopy.filter(id => id !== serviceId);
            } else {
                this.selectedServicesCopy.push(serviceId);
                console.log("selectedServicesCopy:", this.selectedServicesCopy);
            }
            console.log('selectedServicesCopy:', this.selectedServicesCopy);
            this.updateCounter();
        },

        updateDistanceRange(event) {
            this.$emit('update:distanceRange', event.target.value);
            this.updateCounter();
        },
        closeSidebar() {
            this.$emit('close-sidebar');
        },
        
        // Metodo per gestire l'applicazione dei filtri e gli emit al component padre ApartmentHome
        applyFilters() {
            let apiUrl = `http://127.0.0.1:8001/api/v1/?services=${this.selectedServicesCopy.join(",")}`;

            if (this.selectedRooms) {
                apiUrl += `&room=${this.selectedRooms}`;
            }
            if (this.selectedBathrooms) {
                apiUrl += `&bathroom=${this.selectedBathrooms}`;
            }
            
            if (this.selectedSize !== null) {
                apiUrl += `&mq=${this.selectedSize}`;
            }

            if (this.referencePoint) {
                this.$emit('filter-by-distance', this.referencePoint);
            }

            console.log(apiUrl);

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.filteredApartments = data;
                    this.$emit('apartments-updated', this.filteredApartments);
                    this.updateCounter();
                })
                .catch(error => {
                    console.log('There was a problem with the fetch operation:', error.message);
                });

            this.$emit('update-filters', {
                rooms: this.selectedRooms,
                bathrooms: this.selectedBathrooms,
                size: this.selectedSize,
                services: this.selectedServices,
            });
            this.$emit('apply-filters');
            this.$emit('update:distanceRange', this.tempDistanceRange);
            
            this.closeSidebar();
            this.initializeFilters();
        },

        // Metodi per gestire lo scrolling attraverso lo style
        disableBodyScroll() {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.paddingRight = `${scrollbarWidth}px`;
            document.body.style.overflow = 'hidden';
        },
        enableBodyScroll() {
            document.body.style.paddingRight = '0px';
            document.body.style.overflow = '';
        },
    },

    // Watcher per eventi che controlla se la variabile passata da ApartmentHome cambia
    watch: {

        selectedRooms: 'updateCounter',
        selectedBathrooms: 'updateCounter',
        selectedSize: 'updateCounter',
        selectedServices: {
            immediate: true,
            handler(newVal) {
                this.selectedServicesCopy = [...newVal];
            }
        },
        selectedServicesCopy: {
            handler: 'updateCounter',
            deep: true,
        },
        // Watcher per la sidebar in modo tale da rimuoverla dal body se i filtri sono visibili
        isSidebarVisible(newVal) {
            if (newVal) {
                this.disableBodyScroll();
            } else {
                this.enableBodyScroll();
            }
        }
    },
    computed:{
        filteredApartmentsCount() {
            return this.counterFilter !== null ? this.counterFilter : this.apartments.length;
        },
    }
};
</script>

<template>
    <div class="filter-sidebar p-3">

        <!-- Header fisso -->
        <div class="header border-bottom">
            <button class="btn-close" aria-label="Close" @click="closeSidebar"></button>
            <h3>Filtri</h3>
        </div>

        <!-- Sezione scrollabile dei filtri -->
        <div class="scrollable-section">

            <!-- Filtri distanza -->
            <div class="mb-3">
                <label for="distanceRange" class="form-label">Distanza (km)</label>
                <input type="range" class="form-range" v-model="tempDistanceRange" @input="updateCounter" />
                <div>{{ tempDistanceRange }} km</div>
            </div>

            <!-- Numero di stanze -->
            <div class="mb-3">
                <label class="form-label">Stanze:</label>
                <div>
                    <button @click="resetRoomFilter" :class="['btn', selectedRooms === null ? 'btn-primary' : 'btn-outline-primary']">
                        Qualsiasi
                    </button>
                    <button v-for="n in 8" :key="n" 
                        @click="selectedRooms = n" 
                        :class="['btn', selectedRooms === n ? 'btn-primary' : 'btn-outline-primary']">
                        {{ n }}
                    </button>
                    <button @click="selectedRooms = 9" 
                        :class="['btn', selectedRooms === 9 ? 'btn-primary' : 'btn-outline-primary']">
                        9+
                    </button>
                </div>
            </div>

            <!-- Numero di bagni -->
            <div class="mb-3">
                <label class="form-label">Bagni:</label>
                <div>
                    <button @click="resetBathroomFilter" :class="['btn', selectedBathrooms === null ? 'btn-primary' : 'btn-outline-primary']">
                        Qualsiasi
                    </button>
                    <button v-for="n in 5" :key="n" 
                        @click="selectedBathrooms = n" 
                        :class="['btn', selectedBathrooms === n ? 'btn-primary' : 'btn-outline-primary']">
                        {{ n }}
                    </button>
                </div>
            </div>

            <!-- Metri quadrati -->
            <div class="mb-3">
                <label class="form-label">Metri Quadrati:</label>
                <input
                    type="range"
                    min="20"
                    max="500"
                    v-model="selectedSize"
                    step="1"
                    @input="updateCounter"
                />
                <div class="range-values">
                    <span>Filtra da 0 a {{ selectedSize }} mq</span>
                </div>
            </div>

            

            <!-- Title -->
            <h3 id="services" class="my-3">Servizi</h3>

            <!-- Elenco dei servizi -->
            <div class="row">
                <div v-for="service in services" :key="service.id" class="col-6 mb-2">
                    <button
                        :class="['btn', 'btn-light', 'mb-2', selectedServicesCopy.includes(service.id) ? 'active' : '']"
                        @click="updateSelectedServices(service.id)">
                        <i :class="service.icon"></i> {{ service.name }}
                    </button>
                </div>
            </div>

        </div>

        <!-- Footer fisso -->
        <div class="footer border-top">
            <button class="btn btn-outline-danger mt-4" @click="resetAllFilters">Cancella tutto</button>
            <button class="btn btn-primary mt-3 w-100 mt-4" @click="applyFilters">
                Mostra ({{ filteredApartmentsCount }}) appartamenti
            </button>
        </div>

    </div>
</template>

<style lang="scss">

.filter-sidebar {
    position: absolute;
    top: 50%;  
    left: 50%;  
    transform: translate(-50%, -50%);
    height: 90vh; 
    width: 40vw;
    background-color: white;
    z-index: 1000; 
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    scrollbar-width: thin;
    scrollbar-color: transparent transparent; 

    // Header
    .header {
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        position: relative;
        
        h3 {
            flex-grow: 1;
            text-align: center;
        }

        .btn-close {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
    }

    // Sezione scrollabile
    .scrollable-section {
        max-height: calc(85vh - 120px);
        overflow-y: auto;
        padding: 1rem;

        // Personalizza la scrollbar
        &::-webkit-scrollbar-thumb {
            background-color: #888; 
            border-radius: 10px; 
        }

        &::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }
    }

    // Footer
    .footer {
        height: 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
    }
}
</style>
