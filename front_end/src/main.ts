 
import { createApp } from 'vue'
import App from './App.vue'
import "./assets/style.css";

import router from './router/index'
import { createPinia } from 'pinia'
// import './assets/main.css'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { useUserStore } from './hooks/useUser'
 
const pinia = createPinia()

pinia.use(piniaPluginPersistedstate)

const app = createApp(App)

app.use(router)
app.use(pinia)

const userStore = useUserStore()
userStore.fetchUser().then(() => {
    app.mount('#app')
})
