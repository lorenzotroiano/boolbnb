<script>
export default {
    name: "FooterApp",

    data() {
        return {

        }

    }
}
</script>

<template>
    <footer>
        <div class="container px-0 pb-3">
            <div class="p-3">
                <img class="logo-img" src="../assets/img/logoBoolbnb.png" alt="Boolbnb logo">
            </div>
            <div class="row mb-5 m-0">

                <!-- links -->
                <div class="col-12 col-xl-8 d-flex px-0">
                    <div class="row m-0 justify-content-center">
                        <ul class="col-12 col-md-4">
                            <li>
                                <strong>SOCIETA'</strong>
                            </li>
                            <li>
                                <a href="#">Posizioni aperte</a>
                            </li>
                            <li>
                                <a href="#">Stampa</a>
                            </li>
                            <li>
                                <a href="#">Relazioni con gli investitori</a>
                            </li>
                            <li>
                                <a href="#">Boolbnb App - Ovunque tu sia</a>
                            </li>
                            <li>
                                <a href="#">Boolbnb Business Studio</a>
                            </li>
                        </ul>
                        <ul class="col-12 col-md-4">
                            <li>
                                <strong>AIUTO</strong>
                            </li>
                            <li>
                                <a href="#">Scopri come funziona Boolbnb</a>
                            </li>
                            <li>
                                <a href="#">Termini e condizioni</a>
                            </li>
                            <li>
                                <a href="#">Note legali</a>
                            </li>
                            <li>
                                <a href="#">Preferenze sui cookie</a>
                            </li>
                            <li>
                                <a href="#">Informativa sulla privacy</a>
                            </li>
                            <li>
                                <a href="#">Cyber Security</a>
                            </li>
                            <li>
                                <a href="#">COVID-19: consigli per i viaggiatori</a>
                            </li>
                        </ul>
                        <ul class="col-12 col-md-4">
                            <li>
                                <strong>INFORMAZIONI</strong>
                            </li>
                            <li>
                                <a href="#">News</a>
                            </li>
                            <li>
                                <a href="#">Lavora con noi</a>
                            </li>
                            <li>
                                <a href="#">Investitori</a>
                            </li>
                            <li>
                                <a href="#">Sostenibilità</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- socials -->
                <div class="col-12 col-xl-2 d-flex justify-content-center p-0">
                    <div class="d-flex justify-content-center align-items-center flex-column socials mt-4">
                        <h2 class="m-0">Socials</h2>
                        <ul class="d-flex gap-4 pe-4">
                            <li>
                                <a href="#">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa-brands fa-github"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row mt-3 justify-content-center text-center m-0">
                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    Italia © 2023 Boolbnb, Inc. Tutti i diritti riservati
                </span>
            </div>
        </div>
    </footer>
</template>

<style lang="scss" scoped>
footer {
    width: 100%;
    background-color: #323232;

    .container {
        color: #8e8e8e;

        .row {

            .socials {
                ul {
                    li {
                        a {
                            font-size: 2rem;

                        }
                    }
                }
            }

            li {
                list-style-type: none;
                margin-top: 10px;
                font-size: 15px;
                color: #bcabe8;
                padding: 3px;

                a {
                    text-decoration: none;
                    color: #879ae8;
                }

                a:hover {
                    color: #b3caff;
                }
            }
        }
    }

    .p-3 img {
        width: 90px;
    }
}


@media (max-width: 425px) {

    footer {
        .container {
            .row {
                .col-12 {
                    .socials {
                        h2 {
                            font-size: 1.6rem;
                        }

                        ul {
                            padding-left: 0;
                            padding-right: 0;

                            li {
                                a {
                                    font-size: 1.4rem;
                                }
                            }
                        }
                    }
                }

                span {
                    font-size: 0.9rem;
                }
            }
        }
    }
}
</style>
