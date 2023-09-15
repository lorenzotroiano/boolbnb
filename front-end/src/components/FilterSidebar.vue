<script>
export default {
    props: ['services', 'selectedServices'],
    data() {
        return {
            selectedServicesCopy: [...this.selectedServices],
            filteredApartments: [],
            
            selectedRooms: null,
            selectedBathrooms: null,
            selectedSize: null,
        };
    },
    methods: {
        filterByRoomsBathroomsSize(apartment) {
            const roomCondition = !this.selectedRooms || apartment.room === this.selectedRooms;
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
        applyFilters() {
            let apiUrl = `http://127.0.0.1:8001/api/v1/?services=${this.selectedServicesCopy.join(",")}`;

            if (this.selectedRooms) {
                apiUrl += `&room=${this.selectedRooms}`;
            }
            if (this.selectedBathrooms) {
                apiUrl += `&bathroom=${this.selectedBathrooms}`;
            }
            if (this.selectedSize) {
                apiUrl += `&mq=${this.selectedSize}`;
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
        }
    },
};
</script>

<template>
    <div class="filter-sidebar">

        <!-- Numero di stanze -->
        <div class="filter-group">
            <label for="selectedRooms">Stanze:</label>
            <input v-model="selectedRooms" type="number" id="selectedRooms" placeholder="Numero di stanze">
        </div>

        <!-- Numero di bagni -->
        <div class="filter-group">
            <label for="selectedBathrooms">Bagni:</label>
            <input v-model="selectedBathrooms" type="number" id="selectedBathrooms" placeholder="Numero di bagni">
        </div>

        <!-- Metri quadrati -->
        <div class="filter-group">
            <label for="selectedSize">Metri Quadrati:</label>
            <input v-model="selectedSize" type="number" id="selectedSize" placeholder="Metri quadrati">
        </div>

        <!-- Title -->
        <h3 id="services">Servizi</h3>

        <!-- Elenco dei servizi -->
        <div class="row justify-between flex">
            <div v-for="service in services" :key="service.id" class="col-6 mb-2">
                <button
                    :class="{ 'btn-selected': selectedServicesCopy.includes(service.id), 'btn': true, 'btn-icon': true }"
                    @click="updateSelectedServices(service.id)">
                    <i :class="service.icon"></i>
                </button>
                <span>{{ service.name }}</span>
            </div>
        </div>

        <!-- Pulsante "Applica filtri" -->
        <button id="applyFilter" class="btn btn-primary mt-3" @click="applyFilters">Applica filtri</button>
    </div>
</template>

<style lang="scss">
.filter-sidebar {
    #services {
        text-align: left;
        margin-bottom: 50px;
    }

    #applyFilter {
        width: 20%;
        text-align: center;
        border-radius: 4px;
    }

    .btn {
        width: 100%;
        text-align: center;
        border-radius: 4px;

        &:hover {
            cursor: pointer;
        }

        &.btn-selected {
            background-color: dodgerblue;
            color: white;
        }

        &.btn-icon i {
            font-size: 1.5em;
        }
    }
}
</style>
