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
                <div class="logo">
                    <a href="http://localhost:5174"><img src="../assets/img/logoBoolbnb.png" alt=" Logo"></a>
                </div>
                <div class="search">
                    <input type="text" placeholder="Vai ovunque">
                    <button><font-awesome-icon icon="fa-solid fa-magnifying-glass" /></button>
                </div>
                <nav>
                    <ul class="menu">
                        <li><a href="http://127.0.0.1:8000/login">Login</a> </li>
                        <li>Registrati</li>
                        <li>Dashboard</li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
</template>

<style lang="scss" scoped>
@use "../styles/general.scss" as *;

header {
    // position: fixed;
    // width: 100%;
    // background-color: white;

    .container {
        max-width: 80%;
        margin: 0 auto;

        .flex {
            display: flex;
            align-items: center;
            justify-content: space-between;


            .logo {
                width: 180px;

                img {
                    max-width: 100%;
                    max-height: 100%;
                    border-radius: 22px;
                }
            }

            .search {
                background-color: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border-radius: 30px;
                padding: 10px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                max-width: 600px;
                margin: 50px auto;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
                cursor: pointer;
            }

            .search:hover {
                transform: scale(1.05);
                transition: transform 0.2s ease-in-out;
            }

            input {
                border: none;
                padding: 4px;
                border-radius: 30px;
                width: 400px;
                outline: none;

            }

            button {
                background-color: $color-blue;
                color: #fff;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 40px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: $color-blue-hover;
            }

            .menu {
                display: flex;
                align-items: center;
                gap: 26px;


            }
        }

    }


}
</style>
