<script>
export default {
    props: ['services', 'selectedServices', 'referencePoint', 'distanceRange', 'isSidebarVisible'],
    data() {
        return {
            selectedServicesCopy: [...this.selectedServices],
            filteredApartments: [],

            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: 20,

            tempDistanceRange: this.distanceRange, 
        };
    },
    methods: {

        selectRoom(room) {
            this.selectedRooms = room;
        },

        selectBathroom(bathroom) {
            this.selectedBathrooms = bathroom;
        },
        filterByRoomsBathroomsSize(apartment) {
            let roomCondition;
            if (this.selectedRooms === 9) { 
                roomCondition = apartment.room > 8;
            } else {
                roomCondition = apartment.room === this.selectedRooms;
            }

            const bathroomCondition = !this.selectedBathrooms || apartment.bathroom === this.selectedBathrooms;

            let upperSizeLimit;
            if (this.selectedSize === 50) {
                upperSizeLimit = this.selectedSize + 50;
            } else {
                upperSizeLimit = this.selectedSize + 100;
            }

            const sizeCondition = !this.selectedSize || (apartment.mq >= this.selectedSize && apartment.mq < upperSizeLimit);

            return roomCondition && bathroomCondition && sizeCondition;
        },
        filterByServices(apartment) {
            return this.selectedServices.every(serviceId =>
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
        },
        updateDistanceRange(event) {
            this.$emit('update:distanceRange', event.target.value);
        },
        closeSidebar() {
            this.$emit('close-sidebar');
        },
        
        // Metodo per gestire l'applicazione dei filtri e gli emit al component padre ApartmentHome
        applyFilters() {
            let apiUrl = `http://127.0.0.1:8000/api/v1/?services=${this.selectedServicesCopy.join(",")}`;

            if (this.selectedRooms) {
                apiUrl += `&room=${this.selectedRooms}`;
            }
            if (this.selectedBathrooms) {
                apiUrl += `&bathroom=${this.selectedBathrooms}`;
            }
            if (this.selectedSize) {
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
        }
    },

    // Watcher per eventi che controlla se la variabile passata da ApartmentHome cambia
    watch: {
    isSidebarVisible(newVal) {
        if (newVal) {
            this.disableBodyScroll();
        } else {
            this.enableBodyScroll();
        }
    }
}
};
</script>

<template>
    <div class="filter-sidebar p-3">

        <!-- Button per chiudere il component -->
        <button class="btn-close" aria-label="Close" @click="closeSidebar"></button>

        <!-- Filtri distanza -->
        <div class="mb-3">
            <label for="distanceRange" class="form-label">Distanza (km)</label>
            <input type="range" class="form-range" v-model="tempDistanceRange" />
            <div>{{ tempDistanceRange }} km</div>
        </div>

       <!-- Numero di stanze -->
        <div class="mb-3">
            <label class="form-label">Stanze:</label>
            <div>
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
                <button v-for="n in 5" :key="n" 
                    @click="selectedBathrooms = n" 
                    :class="['btn', selectedBathrooms === n ? 'btn-primary' : 'btn-outline-primary']">
                    {{ n }}
                </button>
            </div>
        </div>

        <!-- Metri quadrati -->
        <div class="mb-3">
            <label class="form-label">Metri Quadrati: {{ selectedSize }}</label>
            <input v-model="selectedSize" type="range" min="20" max="500" step="10" class="form-control-range">
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

        <!-- Pulsante "Applica filtri" -->
        <button class="btn btn-primary mt-3 w-100" @click="applyFilters">Applica filtri</button>
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
        overflow-y: scroll;
        border-radius: 10px 10px 10px 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        scrollbar-width: thin;
        scrollbar-color: transparent transparent; 
        
        /* Personalizza la scrollbar */
        .filter-sidebar::-webkit-scrollbar-thumb {
            background-color: #888; /* Cambia il colore del cursore della scrollbar */
            border-radius: 10px; /* Aggiungi bordi arrotondati al cursore */
        }

        .filter-sidebar::-webkit-scrollbar-track {
            background-color: #f1f1f1; /* Cambia il colore del track della scrollbar */
        }

        h3 {
            text-align: left;
        }

        .btn {
            &.active {
                background-color: dodgerblue;
                color: white;
                &:hover {
                    background-color: darkblue;
                }
            }

            i {
                margin-right: 5px;
            }
        }
    }



.filter-sidebar:focus {
    outline: none;
}
</style>
