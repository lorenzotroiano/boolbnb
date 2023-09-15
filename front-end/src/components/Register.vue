<script>


export default {
    name: "Register",

    data() {
        return {
        }
    }
}
</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-quadrant p-4 rounded shadow">
                    <h2 class="mb-4">Registrati</h2>
                    <form @submit.prevent="register">
                        <div class="form-group">
                            <label for="name">Nome:</label>
                            <input type="text" class="form-control" id="name" v-model="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" v-model="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" v-model="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Conferma Password:</label>
                            <input type="password" class="form-control" id="confirmPassword" v-model="confirmPassword"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Data di Compleanno:</label>
                            <input type="date" class="form-control" id="birthdate" v-model="birthdate" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrati</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
// @use '../styles/partials/variables.scss' as *;

footer {
    background-color: rgb(166, 252, 198);
}
</style>
