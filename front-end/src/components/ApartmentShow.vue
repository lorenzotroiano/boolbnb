<script>
import axios from 'axios';
import tt from '@tomtom-international/web-sdk-maps';
export default {
    data() {
        return {
            apartment: [],
            map: null,
            apiKey: "ePmJI0VGJsx4YELF5NbrXSe90uKPnMKK",
        };
    },

    methods: {
        getImageUrl(imageName) {
            return `http://127.0.0.1:8001/storage/${imageName}`;
        },
        initializeTomTomMap() {
            try {
                this.map = tt.map({
                    key: this.apiKey,
                    container: this.$refs.map, // Usa il riferimento al div della mappa
                    center: this.center, // Utilizza this.center come centro iniziale
                    zoom: 10,

                });

                this.map.on('load', () => {
                    new tt.Marker().setLngLat(this.center).addTo(this.map); // Utilizza this.center come posizione del marcatore
                });
            } catch (error) {
                console.error("Errore durante l'inizializzazione della mappa TomTom:", error);
            }
        },
    },
    props: ['id'],
    mounted() {
        axios.get(`http://127.0.0.1:8001/api/v1/show/${this.id}`)
            .then(response => {
                this.apartment = response.data.apartment;
                this.center = [this.apartment.latitude, this.apartment.longitude];
            })
            .catch(error => {
                console.log(error);
            })
        this.initializeTomTomMap();
    },
};
</script>

<template>
    <main>
        <div class="container">
            <h1>{{ apartment.name }}</h1>

            <span class="address"> <i class="fa-solid fa-map"></i>- {{ apartment.address }}</span>

            <div class="flex-map">
                <img :src="getImageUrl(apartment.cover)" alt="Apartment Image">

                <div ref="map" style="width: 35%; height: 400px;"></div>
            </div>

            <!-- <p>ID: {{ apartment.id }}</p> -->
            <div class="apartment-description">
                <h3>Info Appartamento</h3>
                <p>{{ apartment.description }}</p>

                <div class="description-flex">
                    <button class="custom-button">
                        <i class="fa-solid fa-house"></i>
                        <span class="button-text">{{ apartment.room }}</span>
                    </button>
                    <button class="custom-button">
                        <i class="fa-solid fa-toilet"></i>
                        <span class="button-text">{{ apartment.bathroom }}</span>
                    </button>
                    <button class="custom-button">
                        <i class="fa-brands fa-squarespace"></i>
                        <span class="button-text">{{ apartment.mq }} Mq</span>
                    </button>
                </div>


            </div>

            <!-- <p>Latitudine: {{ apartment.latitude }}</p>
        <p>Longitudine: {{ apartment.longitude }}</p> -->
            <!-- <p>Visibile: {{ apartment.visible }}</p>
        <p>Sponsorizzato: {{ apartment.sponsor }}</p> -->


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

        </div>
    </main>
</template>




<style scoped lang="scss">
main {
    background-color: rgb(255, 255, 255);


    .container {
        max-width: 80%;
        margin: 0 auto;
        padding-top: 200px;
        padding-bottom: 30px;

        p {
            color: grey;
        }

        span {
            color: grey;
        }

        i {
            color: grey;
        }

        .address {
            font-size: 20px;
        }

        .flex-map {

            margin: 50px 0;
            display: flex;
            justify-content: space-between;

            img {
                height: 400px;
                width: 60%;
            }
        }

        .apartment-description {
            border-top: 1px black solid;
            font-size: 20px;

            padding: 45px 0;

            h3 {
                text-align: center;
                margin-bottom: 40px;
            }


            .description-flex {
                border-bottom: 1px black solid;
                display: flex;
                justify-content: space-around;
                font-size: 24px;


                padding: 40px 0;


                .custom-button {
                    border: none;
                    border-radius: 12px;
                    background-color: white;
                    padding: 5px 10px;

                    cursor: pointer;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                    font-size: 20px;
                    display: flex;
                    align-items: center;
                }

                .custom-button:hover {
                    background-color: #f0f0f0;
                    /* Colore grigio chiaro al passaggio del mouse */
                    transform: scale(1.05);
                    /* Ingrandimento al passaggio del mouse */
                }

                .button-text {
                    margin: 0 15px;
                }
            }


        }



        // background-color: chartreuse;


    }

    .services {

        margin: 90px 0;
    }

}



.service-icon {
    font-size: 20px;

}


.card:hover {
    background-color: #f0f0f0;

    color: #fff;

}
</style>
