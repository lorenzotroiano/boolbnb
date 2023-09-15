<script>
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
    setup() {
        const router = useRouter();
        const form = {
            email: '',
            password: ''
        };

        const handleLogin = async () => {
            try {
                await axios.post('/login', {
                    email: form.email,
                    password: form.password
                });
                // Puoi effettuare il redirect a '/dashboard' dopo il login avvenuto con successo
                router.push('/dashboard');
            } catch (error) {
                // Gestisci eventuali errori qui
                console.error('Errore durante il login:', error);
            }
        };

        return {
            form,
            handleLogin
        };
    }
};
</script>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-quadrant p-4 rounded shadow">
                    <h2 class="mb-4">Login</h2>
                    <form @submit.prevent="handleLogin">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" v-model="form.email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" v-model="form.password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Accedi</button>
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
