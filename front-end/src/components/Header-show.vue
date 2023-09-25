<script>
import axios from 'axios';




export default {
    name: "HeaderShowVue",
    components: {
    },
    data() {

    },
}
</script>

<template>
    <header>
        <div class="container">
            <div id="bar">

                <!-- Logo immagine -->
                <div class="logo">
                    <a href="http://localhost:5174"><img src="../assets/img/Boolbnb.png" alt=" Logo"></a>
                </div>

                <!-- Nav -->
                <nav>
                    <ul class="menu">
                        <li><a class="button-52" href="http://127.0.0.1:8001/login">Login</a> </li>
                        <li><a class="button-52" href="http://127.0.0.1:8001/register">Registrati</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
</template>


<style lang="scss" scoped>
@use '../styles/general.scss';
@use '../styles/partials/headerShow.scss';
@use "../styles/partials/_variables" as *;
</style>
