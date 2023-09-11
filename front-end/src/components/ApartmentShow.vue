<script>
import axios from 'axios';

export default {
    data() {
        return {
            apartments: [],

        }
    },
    mounted() {
        axios.get('http://127.0.0.1:8001/api/v1/')
            .then(response => {
                const data = response.data.apartments;
                this.apartments = data.data;
            })
            .catch(error =>{
                console.log(error);
            })
    },
};



</script>

<template>
    
</template>

<style scoped>
</style>
