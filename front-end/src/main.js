import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { router } from './router.js';
import 'bootstrap/dist/css/bootstrap.min.css'
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faMagnifyingGlass } from '@fortawesome/free-solid-svg-icons'

library.add(faMagnifyingGlass)
createApp(App).use(router).component('font-awesome-icon', FontAwesomeIcon).mount('#app')
