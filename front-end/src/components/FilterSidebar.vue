<script>

export default {
    props: {
        selectedServices: {
            type: Array,
            default: () => []
        },
        services: Array,
        referencePoint: Object,
        distanceRange: Number,
        isSidebarVisible: Boolean,
        apartments: {
            type: Array,
            default: () => []
        },
        isSearchClicked: Boolean,
        tempSize: Number
    },
    data() {
        return {
            // Copia di selected services perché le props sono readonly
            selectedServicesCopy: [...this.selectedServices],

            filteredApartments: [],

            // Copia di apartments perché le props sono readonly
            // localApartments: this.apartments,

            counterFilter: null,
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,    
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
            return this.selectedServicesCopy.every(serviceId =>
                apartment.services.some(service => service.id === serviceId)
            );
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

        handleSliderChange() {
            this.$emit('update:distanceRange', this.tempDistanceRange);
            this.updateCounter();
        },

        closeSidebar() {
            this.$emit('close-sidebar');
        },

        // Metodo per gestire l'applicazione dei filtri e gli emit al component padre ApartmentHome
        applyFilters() {
            this.filteredApartments = this.apartments.filter((apartment) => {
                return (
                    this.filterByRooms(apartment) &&
                    this.filterByBathrooms(apartment) &&
                    this.filterBySize(apartment) &&
                    this.filterByServices(apartment)
                );
            });

            this.$emit('apartments-updated', this.filteredApartments);
            this.updateCounter();
            
            this.$emit('update-filters', {
                rooms: this.selectedRooms,
                bathrooms: this.selectedBathrooms,
                size: this.selectedSize,
                services: this.selectedServices,
            });
            this.$emit('apply-filters');

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

    watch: {

        // Watcher sugli apartments per fare l'update del counter
        apartments: {
            immediate: true,
            handler() {
                this.updateCounter();
            }
        },

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
        },
    },
    computed: {
        filteredApartmentsCount() {
            return this.counterFilter !== null ? this.counterFilter : (this.localApartments ? this.localApartments.length : 0);
        },
    },
    mounted() {

        this.updateCounter();
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

            <!-- Stanze -->
            <div class="mb-3">
                <label class="form-label d-flex justify-content-center">Stanze:</label>
                <div class="btn-group" role="group">
                    <button @click="resetRoomFilter"
                        :class="['btn', selectedRooms === null ? 'btn-dark' : 'btn-outline-dark', 'btn-sm', 'rounded-pill', 'me-2', 'px-3']">
                        Qualsiasi
                    </button>
                    <button v-for="n in 8" :key="n" @click="selectedRooms = n"
                        :class="['btn', selectedRooms === n ? 'btn-dark' : 'btn-outline-dark', 'btn-sm', 'rounded-pill', 'me-2', 'px-3']">
                        {{ n }}
                    </button>
                    <button @click="selectedRooms = 9"
                        :class="['btn', selectedRooms === 9 ? 'btn-dark' : 'btn-outline-dark', 'btn-sm', 'rounded-pill', 'px-3']">
                        9+
                    </button>
                </div>
            </div>

            <!-- Bagni -->
            <div class="mb-3">
                <label class="form-label d-flex justify-content-center">Bagni:</label>
                <div class="btn-group" role="group">
                    <button @click="resetBathroomFilter"
                        :class="['btn', selectedBathrooms === null ? 'btn-dark' : 'btn-outline-dark', 'btn-sm', 'rounded-pill', 'me-2', 'px-3']">
                        Qualsiasi
                    </button>
                    <button v-for="n in 5" :key="n" @click="selectedBathrooms = n"
                        :class="['btn', selectedBathrooms === n ? 'btn-dark' : 'btn-outline-dark', 'btn-sm', 'rounded-pill', 'me-2', 'px-3']">
                        {{ n }}
                    </button>
                </div>
            </div>


            <!-- Metri quadrati -->
            <div class="mb-3">
                <label class="form-label">Metri Quadrati:</label>
                <input type="range" min="20" max="500" v-model="selectedSize" step="1" @input="updateCounter" />
                <div class="range-values">
                    <span>Filtra da 0 a {{ selectedSize }} mq</span>
                </div>
            </div>

            <!-- Title -->
            <h3 id="services" class="my-3">Servizi</h3>

            <!-- Elenco dei servizi -->
            <div class="row">
                <div v-for="service in services" :key="service.id" class="col-6 mb-3">
                    <div class="card shadow-sm"
                        :class="selectedServicesCopy.includes(service.id) ? 'bg-dark text-white' : ''"
                        @click="updateSelectedServices(service.id)">
                        <div class="card-body text-center">
                            <i :class="['fa', service.icon]"></i>
                            <div>{{ service.name }}</div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Footer fisso -->
        <div class="footer border-top">
            <button class="btn btn-outline-danger" @click="resetAllFilters">Cancella tutto</button>
            <button class="btn btn-dark" @click="applyFilters">
                Mostra ({{ counterFilter  }}) alloggi
            </button>
        </div>

    </div>
</template>

<style lang="scss">
.filter-sidebar {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    height: 90vh;
    height: 90vh;
    width: 40vw;
    background-color: white;
    /* Sfondo bianco */
    z-index: 99999;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    /* Shadow box più pronunciata */
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
            color: black;
            /* Testo nero */
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

    .card:hover {
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.4);
        transform: translateY(-3px);
        transition: box-shadow 0.2s ease, transform 0.3s ease;
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
