<script>
import { RouterLink } from 'vue-router';
import HeaderApp from './components/HeaderApp.vue'

import FooterApp from './components/FooterApp.vue'

export default {
    components: {

        HeaderApp,
        FooterApp,

    }
}


</script>

<template>
    <HeaderApp />
    <router-view></router-view>
    <FooterApp />
</template>

<style lang="scss">
@use "./styles/general.scss" as *;

li {
    list-style: none;

}

// i {
//     padding: 5px;
// }
</style>
