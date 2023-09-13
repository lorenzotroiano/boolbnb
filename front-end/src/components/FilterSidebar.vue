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

        <h3>Filtri</h3>

        <!-- Elenco dei servizi -->
        <div v-for="service in services" :key="service.id" class="form-check">
            <input class="form-check-input" type="checkbox" :value="service.id"
                :checked="selectedServicesCopy.includes(service.id)" @change="updateSelectedServices(service.id)" />
            <label :for="'filter-service-' + service.id">{{ service.name }}</label>
        </div>

        <!-- Pulsante "Applica filtri" -->
        <button class="btn btn-primary" @click="applyFilters">Applica filtri</button>
    </div>
</template>


<style></style>
