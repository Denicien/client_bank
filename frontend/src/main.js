import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'   // ← IMPORTANT : importer router

const app = createApp(App)
app.use(router)                 // ← IMPORTANT : utiliser router
app.mount('#app')