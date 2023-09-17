<script>


export default {
    name: "HeaderApp",
    data() {
        return {

        }

    }
}
</script>

<template>
    <header>
        <div class="container">
            <div class="flex">

                <!-- Logo immagine -->
                <div class="logo">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb.png" alt=" Logo"></a>
                </div>

                <!-- Logo -->
                <div class="logo-mobile">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb-mobile.png" alt="Logo"></a>
                </div>

                <!-- Searchbar -->
                <div class="search">
                    <input type="text" placeholder="Vai ovunque">
                    <button><font-awesome-icon icon="fa-solid fa-magnifying-glass" /></button>
                </div>

                <!-- Nav -->
                <nav>
                    <ul class="menu">
                        <li><a href="http://127.0.0.1:8000/login">Login</a> </li>
                        <li><a href="http://127.0.0.1:8000/register">Registrati</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
</template>

<style lang="scss" scoped>
@use '../styles/general.scss';
@use '../styles/partials/_header.scss';
@use '../styles/partials/_variables.scss' as *;
</style>
