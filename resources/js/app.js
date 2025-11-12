import './bootstrap'
import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { MotionPlugin } from '@vueuse/motion'
import i18n from './i18n'
import { useAppStore } from './store'
import App from './App.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App: InertiaApp, props, plugin }) {
        const pinia = createPinia()

        const app = createApp({ 
            render: () => h(App, null, {
                default: () => h(InertiaApp, props)
            })
        })
            .use(plugin)
            .use(pinia)
            .use(i18n)
            .use(MotionPlugin)

        // Initialize theme settings after Pinia is available
        const appStore = useAppStore()
        appStore.initializeTheme()

        // Set initial locale from server
        if (props.initialPage.props.locale) {
            i18n.global.locale.value = props.initialPage.props.locale
            localStorage.setItem('locale', props.initialPage.props.locale)
        }

        app.mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
