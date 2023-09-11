<script>
import axios from 'axios';

export default {
    data() {
        return {
            apartment: [],

        }
    },
    props: ['id'],
    mounted() {
        axios.get(`http://127.0.0.1:8000/api/v1/show/${this.id}`)
            .then(response => {
                this.apartment = response.data.apartment;
            })
            .catch(error => {
                console.log(error);
            })
    },
};



</script>

<template>
    
</template>

<style scoped>
</style>
