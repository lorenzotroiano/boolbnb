<script>
export default {
    props: ['services', 'selectedServices'],
    data() {
        return {
            // Crea una copia dei servizi selezionati per gestirli all'interno del componente
            selectedServicesCopy: [...this.selectedServices],
        };
    },
    methods: {
        updateSelectedServices(serviceId) {
            if (this.selectedServicesCopy.includes(serviceId)) {
                this.selectedServicesCopy = this.selectedServicesCopy.filter(id => id !== serviceId);
            } else {
                this.selectedServicesCopy.push(serviceId);
            }
        },
        applyFilters() {
            // Passa i filtri selezionati al componente principale
            this.$emit('apply-filters', this.selectedServicesCopy);
        }
    },
};
</script>


<template>
    <div class="filter-sidebar">

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

        &.btn-selected {
            background-color: dodgerblue;
            color: white;
        }

        &.btn-icon i {
            font-size: 1.5em; // Aumenta la dimensione dell'icona se necessario
        }
    }
}
</style>
