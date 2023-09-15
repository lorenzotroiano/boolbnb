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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="d-flex justify-content-between w-100">
            <a class="navbar-brand mr-auto" href="#">Header Boolbnb</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link :to="{ name: 'login' }" class="nav-link">Login</router-link>
                </li>
                <li class="nav-item">
                    <router-link :to="{ name: 'register' }" class="nav-link">Register</router-link>

                </li>
            </ul>
        </div>
    </nav>
</template>

<style lang="scss" scoped>
nav {
    background-color: violet;
}
</style>
