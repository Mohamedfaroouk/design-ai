import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { MotionPlugin } from '@vueuse/motion'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import { useAppStore } from './store'
import '../css/app.css'

// Create the app
const app = createApp(App)

// Use plugins
const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(i18n)
app.use(MotionPlugin)

// Initialize theme settings
const appStore = useAppStore()
appStore.initializeTheme()

// Mount the app
app.mount('#app')
