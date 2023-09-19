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
            <div class="flex-info">
                <div class="img">
                    <img v-if="apartment.cover" :src="getImageUrl(apartment.cover)" alt="Apartment Image">
                </div>

                <div class="apartment-description">
                    <h3>Info Appartamento</h3>
                    <div class="description-flex">
                        <span class="">Stanze: {{ apartment.room }}</span>
                        <span class="">Bagni: {{ apartment.bathroom }}</span>
                        <span class="">Mq: {{ apartment.mq }}</span>
                    </div>
                    <div class="only-description">
                        <p>{{ apartment.description }}</p>
                    </div>



                    <!-- Servizi -->
                    <span v-if="apartment && apartment.services && apartment.services.length > 0">Servizi
                        disponibili:</span>
                    <span v-else>Nessun servizio disponibile</span>

                    <div class="services" v-if="apartment && apartment.services && apartment.services.length > 0">
                        <!-- Centra gli elementi orizzontalmente -->
                        <div class="" v-for="service in apartment.services" :key="service.id">
                            <!-- Riduci la larghezza delle colonne per fare spazio -->
                            <div class="">
                                <div class="label-services">
                                    <i :class="service.icon" class="service-icon"></i>
                                    <!-- Riduci la dimensione dell'icona -->
                                    <span class="">{{ service.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>





            <!-- Mappa -->
            <h3>Mappa</h3>
            <div ref="map" style="width: 75%; margin: 30px auto; height: 400px;"></div>

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

        margin: 0 auto;
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

        .flex-info {

            display: flex;
            justify-content: space-between;

            padding: 80px;
            height: 550px;

            .img {

                border-radius: 20px;
                width: 40%;
                height: 100%;

                img {

                    max-height: 100%;
                    object-fit: cover;
                }
            }

            .apartment-description {
                width: 50%;

                padding: 20px;

                span {
                    color: gray;
                }

                .only-description {
                    width: 100%;
                }


                .description-flex {
                    text-align: center;
                    padding: 10px;


                    span {
                        color: $color-blue-hover;
                        margin: 0 30px;
                    }




                }
            }
        }







        .message-form {
            border-top: 0.1px solid gray;

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

        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 15px;


        .label-services {
            border: 1px solid blue;
            width: 150px;
            display: flex;

            align-items: center;

            padding: 8px;

            text-align: center;
        }

        i {
            color: $color-blue-hover;
            font-size: 16px;
            margin-right: 10px;

        }

        // span {
        //     font-size: 15px;
        // }





    }
}
</style>
