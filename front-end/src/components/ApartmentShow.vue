<script>
import axios from 'axios';
// import tt from '@tomtom-international/web-sdk-maps';
import * as tt from '@tomtom-international/web-sdk-maps';
import HeaderApp from './HeaderApp.vue';

export default {
    name: 'ApartmentShow',
    components: {
        HeaderApp
    },
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
        getCoverUrl(imageName) {
            return `http://127.0.0.1:8000/storage/${imageName}`;
        },

        getImagesUrl(imageName) {
            return `http://127.0.0.1:8000/storage/${imageName}`;
        },
        initializeTomTomMap() {
            try {

                this._nonReactiveMap = tt.map({
                    container: 'map',
                    key: this.apiKey,
                    center: this.center,
                    zoom: 11,
                    container: 'map',
                    key: this.apiKey,
                    center: this.center,
                    zoom: 11,
                });

                this._nonReactiveMap.on('load', () => {
                    let marker = new tt.Marker().setLngLat(this.center);
                    marker.addTo(this._nonReactiveMap);
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
    <HeaderApp></HeaderApp>
    <link rel='stylesheet' href='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.14.0/maps/maps.css'>
    <main>
        <div class="container">

            <div class="row margin-top-140">
                <div>
                    <h1>{{ apartment.name }}</h1>
                    <span class="address d-block text-secondary mb-3"> <i class="fa-solid fa-map"></i> - {{
                        apartment.address }}</span>
                </div>
                <!-- Colonna dell'immagine per schermi grandi -->
                <div class="col-lg-6 col-md-12 image-container rounded overflow-hidden">
                    <img v-if="apartment.cover" :src="getCoverUrl(apartment.cover)" alt="Apartment Image"
                        class="img-fluid main-image mb-2 rounded-top">
                    <div class="d-flex secondary-images-container rounded-bottom">
                        <div v-if="apartment && apartment.images" style="display: flex;justify-content: space-between;">
                            <img class="secondary-image" v-for="image in apartment.images" :key="image.id"
                                :src="getImagesUrl(image.image)" alt="Image">
                        </div>
                    </div>
                </div>


                <!-- Colonna delle informazioni -->
                <div class="col-lg-6 col-md-12">
                    <!-- Info Appartamento -->
                    <div class="apartment-info mb-3 mb-lg-0">
                        <!-- Mappa -->
                        <div class="mt-3 mb-3">
                            <div ref="mapContainer" id="map" class="map-lg align-items-end" style="border-radius: 10px;">
                            </div>
                        </div>
                        <p>{{ apartment.description }}</p>
                        <div id="icon-wrapper" class="d-flex flex-wrap justify-content-start">
                            <!-- Icona della casa -->
                            <div
                                class="d-flex align-items-center justify-content-center mb-2 text-white info-color rounded p-1 me-2 flex-grow-1">
                                <i class="fas fa-home me-2"></i>
                                <span>Stanze: {{ apartment.room }}</span>
                            </div>

                            <!-- Icona del gabinetto -->
                            <div
                                class="d-flex align-items-center justify-content-center mb-2 text-white info-color rounded p-1 me-2 flex-grow-1">
                                <i class="fas fa-toilet me-2"></i>
                                <span>Bagni: {{ apartment.bathroom }}</span>
                            </div>

                            <!-- Icona dei metri quadrati -->
                            <div
                                class="d-flex align-items-center justify-content-center mb-2 text-white info-color rounded p-1 flex-grow-1">
                                <i class="fas fa-ruler-combined me-2"></i>
                                <span>Mq: {{ apartment.mq }}</span>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Mappa per schermi grandi (posizionata qui per fluire sotto la descrizione) -->
            </div>

            <div class="flex-section">

                <div class="services mt-4">
                    <h3>Servizi</h3>
                    <ul>
                        <li class="list-service" v-for="service in  apartment.services " :key="service.id">
                            <i :class="service.icon"></i>
                            <span>{{ service.name }}</span>
                        </li>
                    </ul>
                </div>
                <!-- Messaggi -->

                <div class="message-form mt-4">
                    <h3 class="text-center">Contattaci</h3>
                    <form @submit.prevent="sendMessage">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" v-model="formData.name" required
                                placeholder="Inserisci il tuo nome..">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" v-model="formData.email" required
                                placeholder="Insirisci la tua email..">
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label">Testo</label>
                            <textarea class="form-control" id="body" rows="3" v-model="formData.body" required
                                placeholder="Inserisci testo.."></textarea>
                        </div>
                        <div id=" " class="text-center d-flex justify-content-center"> <button type="submit"
                                class="btn btn-primary">Invia</button></div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</template>

<style scoped lang="scss">
// @use '../styles/general.scss';
@use '../styles/partials/_show.scss';
@use "../styles/partials/_variables" as *;
</style>
