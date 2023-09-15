<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    data() {
        return {
            user: null
        };
    },
    mounted() {
        this.fetchUser();
    },
    methods: {
        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
                console.log(this.user);
            } catch (error) {
                console.error('Errore durante il recupero dei dati dell\'utente:', error);
            }
        }
    }
};
</script>

<template>
    <div>
        <h2>dashboard</h2>
        <!-- Ora puoi utilizzare this.user nei tuoi template per visualizzare i dati dell'utente -->
        <h3>Benvenuto {{ this.user.name }} ora puoi inserire i tuoi appartamenti</h3>
    </div>
</template>





<style scoped></style>
