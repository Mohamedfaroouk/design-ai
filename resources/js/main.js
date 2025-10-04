import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { MotionPlugin } from '@vueuse/motion'
import router from './router'
import '../css/app.css'

// Get the root element
const app = createApp({
    created() {
        // Set up RTL/LTR based on data-dir attribute
        const dir = document.getElementById('app')?.getAttribute('data-dir') || 'ltr'
        document.documentElement.setAttribute('dir', dir)
    }
})

// Use plugins
app.use(createPinia())
app.use(router)
app.use(MotionPlugin)

// Mount the app
app.mount('#app')
