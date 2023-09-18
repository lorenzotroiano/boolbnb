<script>
import axios from 'axios';
import tt from '@tomtom-international/web-sdk-maps';

export default {
    data() {
        return {
            apartment: [],
            map: null,
            apiKey: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
            formData: {
                name: '',
                email: '',
                body: ''
            }
        };
    },

    methods: {
        getImageUrl(imageName) {
            return `http://127.0.0.1:8000/storage/${imageName}`;
        },
        initializeTomTomMap() {
            try {
                this.map = tt.map({
                    key: this.apiKey,
                    container: this.$refs.map, // Usa il riferimento al div della mappa
                    center: this.center, // Utilizza this.center come centro iniziale
                    zoom: 15,

                });

                // Aggiungi un marker alle coordinate specificate


                this.map.on('load', () => {

                    new tt.Marker().setLngLat(this.center).addTo(this.map); // Utilizza this.center come posizione del marcatore
                });
            } catch (error) {
                console.error("Errore durante l'inizializzazione della mappa TomTom:", error);
            }
        },
        sendMessage() {
            axios.post(`http://127.0.0.1:8000/api/v1/show/${this.id}/messages`, this.formData)
                .then(response => {
                    console.log(response.data);
                    this.formData = { name: '', email: '', body: '' };
                    console.log("Messaggio inviato");
                })
                .catch(error => {
                    console.error("Errore durante l'invio del messaggio:", error);
                });
        }
    },
    props: ['id'],
    mounted() {
        axios.get(`http://127.0.0.1:8000/api/v1/show/${this.id}`)
            .then(response => {
                this.apartment = response.data.apartment;
                this.center = [this.apartment.longitude, this.apartment.latitude];
                this.initializeTomTomMap(); // Move this inside the then() block after setting this.center
            })
            .catch(error => {
                console.log(error);
            });
    },
    beforeDestroy() {
        if (this.map) {
            this.map.remove();
        }
    }
};
</script>

<template>
    <link rel='stylesheet' href='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.14.0/maps/maps.css'>
    <main>
        <div class="container">
            <h1>{{ apartment.name }}</h1>
            <span class="address"> <i class="fa-solid fa-map"></i>- {{ apartment.address }}</span>
            <div class="img">
                <img v-if="apartment.cover" :src="getImageUrl(apartment.cover)" alt="Apartment Image">
            </div>

            <!-- <p>ID: {{ apartment.id }}</p> -->
            <div class="apartment-description">
                <h3>Info Appartamento</h3>
                <p>{{ apartment.description }}</p>

                <div class="description-flex">
                    <button class="btn">
                        <i class="fa-solid fa-house"></i>
                        <span class="button-text">{{ apartment.room }}</span>
                    </button>
                    <button class="btn">
                        <i class="fa-solid fa-toilet"></i>
                        <span class="button-text">{{ apartment.bathroom }}</span>
                    </button>
                    <button class="btn">
                        <i class="fa-brands fa-squarespace"></i>
                        <span class="button-text">{{ apartment.mq }} Mq</span>
                    </button>
                </div>
            </div>

            <!-- Servizi -->
            <h2 v-if="apartment && apartment.services && apartment.services.length > 0">Servizi disponibili:</h2>
            <h2 v-else>Nessun servizio disponibile</h2>

            <div class="row justify-content-center services"
                v-if="apartment && apartment.services && apartment.services.length > 0">
                <!-- Centra gli elementi orizzontalmente -->
                <div class="col-md-3" v-for="service in apartment.services" :key="service.id">
                    <!-- Riduci la larghezza delle colonne per fare spazio -->
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <i :class="service.icon" class="service-icon"></i>
                            <!-- Riduci la dimensione dell'icona -->
                            <p class="card-text">{{ service.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mappa -->
            <h3>Mappa</h3>
            <div ref="map" style="width: 75%; margin: 30px auto; height: 400px"></div>

            <!-- Messaggi -->
            <div class="message-form">
                <h3>Invia un messaggio</h3>
                <form @submit.prevent="sendMessage">
                    <div class="mb-3 form-group">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" v-model="formData.name" required
                            placeholder="Inserisci il tuo nome..">
                    </div>
                    <div class="mb-3 form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" v-model="formData.email" required
                            placeholder="Insirisci la tua email..">
                    </div>
                    <div class="mb-3 form-group">
                        <label for="body" class="form-label">Testo</label>
                        <textarea class="form-control" id="body" rows="3" v-model="formData.body" required
                            placeholder="Inserisci testo.."></textarea>
                    </div>
                    <div class="text-center"> <button type="submit" class="btn">Invia</button></div>
                </form>
            </div>
        </div>
    </main>
</template>




<style scoped lang="scss">
@use "../styles/partials/variables" as *;

main {
    background-color: rgb(255, 255, 255);

    h2,
    h3 {
        text-align: center;
    }

    .container {
        max-width: 80%;
        margin: 0 auto;
        padding-top: 140px;
        padding-top: 140px;
        padding-bottom: 30px;

        p {
            color: grey;
        }

        span {
            color: rgb(255, 255, 255);
        }

        i {
            color: rgb(255, 255, 255);
        }

        .address {
            font-size: 20px;
            color: grey;

            i {
                color: grey;
            }
        }

        .img {
            text-align: center;
            padding: 50px;
            margin: 35px 0;

            img {
                height: 400px;
                width: 80%;
            }
        }

        .apartment-description {
            border-top: 1px black solid;
            font-size: 20px;
            padding: 45px 0;

            h3 {
                margin-bottom: 40px;
            }

            .description-flex {
                border-bottom: 1px black solid;
                display: flex;
                justify-content: space-around;
                font-size: 24px;
                padding: 40px 0;

                .btn {
                    border: none;
                    border-radius: 12px;
                    background-color: $color-blue-hover;
                    padding: 5px 10px;
                    cursor: auto;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                    font-size: 20px;
                    display: flex;
                    align-items: center;
                }

                .btn:hover {
                    background-color: $color-dark-purple;
                }

                .button-text {
                    margin: 0 15px;
                }
            }
        }



        .message-form {
            border-top: 0.1px solid black;
            border-bottom: 0.1px solid black;
            padding: 40px;

            form {
                width: 40%;
                margin: 0 auto;
                margin-top: 60px;

                .btn {
                    background-color: $color-blue-hover;
                    color: white;
                }
            }
        }
    }



    .services {
        margin: 40px 0;
        padding: 50px;
        border-bottom: 0.1px solid black;

        .card {
            background-color: $color-blue-hover;

            p {
                color: white;
            }
        }
    }
}

.service-icon {
    font-size: 20px;
}

.card:hover {
    background-color: $color-dark-purple;
    color: #fff;
}
</style>
