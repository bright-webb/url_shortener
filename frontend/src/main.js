import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import 'semantic-ui-css/semantic.min.css';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'



createApp(App).use(router).mount('#app')

