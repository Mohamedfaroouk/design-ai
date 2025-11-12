<template>
    <div class="min-h-screen transition-colors duration-300 relative overflow-hidden"
         :class="appStore.darkMode
             ? 'galaxy-bg'
             : 'bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30'">

        <!-- Shooting Stars (Dark Mode Only) -->
        <template v-if="appStore.darkMode">
            <div class="star-field"></div>
            <div
                v-for="star in shootingStars"
                :key="star.id"
                class="shooting-star"
                :style="{
                    top: star.top,
                    left: star.left,
                    animationDelay: star.delay,
                    animationDuration: star.duration
                }"
            ></div>
        </template>

        <!-- Magic Waving Stars (Light Mode Only) -->
        <template v-else>
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div
                    v-for="star in magicStars"
                    :key="star.id"
                    class="magic-star"
                    :class="star.color"
                    :style="{
                        top: star.top,
                        left: star.left,
                        animationDelay: star.delay,
                        animationDuration: star.duration
                    }"
                >
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2L12 16.8l-6.4 4.4 2.4-7.2-6-4.8h7.6z" />
                    </svg>
                </div>
            </div>
        </template>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="inline-block w-16 h-16 border-4 border-t-primary border-r-transparent border-b-primary border-l-transparent rounded-full animate-spin"></div>
                <p class="mt-4 text-lg transition-colors"
                   :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Loading...
                </p>
            </div>
        </div>

        <!-- Main App -->
        <template v-else>
            <!-- Sidebar -->
            <Sidebar />

            <!-- Main Content Area -->
            <div
                class="transition-all duration-300 relative z-10"
                :class="[
                    appStore.sidebarOpen && !isMobile ? 'ms-72' : 'ms-0',
                    !appStore.sidebarOpen && !isMobile ? 'ms-20' : ''
                ]"
            >
                <!-- Topbar -->
                <Topbar />

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8 mt-20">
                    <router-view v-slot="{ Component }">
                        <transition
                            enter-active-class="transition-all duration-300"
                            enter-from-class="opacity-0 translate-y-4"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-200"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-4"
                            mode="out-in"
                        >
                            <component :is="Component" />
                        </transition>
                    </router-view>
                </main>
            </div>

            <!-- Toast Notifications -->
            <Toast />
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import authService from '@/services/auth'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'
import Toast from '@/components/ui/Toast.vue'

const appStore = useAppStore()
const toast = useToastStore()
const router = useRouter()
const isMobile = ref(false)
const loading = ref(true)

// Generate random shooting stars
const shootingStars = ref([
    { id: 1, top: '10%', left: '20%', delay: '0s', duration: '3s' },
    { id: 2, top: '30%', left: '60%', delay: '2s', duration: '3.5s' },
    { id: 3, top: '50%', left: '40%', delay: '4s', duration: '3s' },
    { id: 4, top: '20%', left: '80%', delay: '6s', duration: '4s' },
    { id: 5, top: '70%', left: '10%', delay: '8s', duration: '3s' },
    { id: 6, top: '40%', left: '70%', delay: '10s', duration: '3.5s' },
    { id: 7, top: '60%', left: '30%', delay: '12s', duration: '4s' },
    { id: 8, top: '15%', left: '50%', delay: '14s', duration: '3s' },
])

// Generate magic colorful waving stars for light mode
const magicStars = ref([
    { id: 1, top: '10%', left: '15%', delay: '0s', duration: '4s', color: 'star-purple' },
    { id: 2, top: '25%', left: '75%', delay: '0.5s', duration: '5s', color: 'star-pink' },
    { id: 3, top: '40%', left: '20%', delay: '1s', duration: '4.5s', color: 'star-blue' },
    { id: 4, top: '15%', left: '85%', delay: '1.5s', duration: '5.5s', color: 'star-yellow' },
    { id: 5, top: '60%', left: '10%', delay: '2s', duration: '4s', color: 'star-cyan' },
    { id: 6, top: '75%', left: '60%', delay: '2.5s', duration: '5s', color: 'star-magenta' },
    { id: 7, top: '50%', left: '90%', delay: '3s', duration: '4.5s', color: 'star-amber' },
    { id: 8, top: '30%', left: '40%', delay: '3.5s', duration: '5.5s', color: 'star-indigo' },
    { id: 9, top: '80%', left: '25%', delay: '4s', duration: '4s', color: 'star-rose' },
    { id: 10, top: '20%', left: '50%', delay: '0.8s', duration: '5s', color: 'star-teal' },
    { id: 11, top: '65%', left: '80%', delay: '1.2s', duration: '4.5s', color: 'star-violet' },
    { id: 12, top: '45%', left: '65%', delay: '2.2s', duration: '5.5s', color: 'star-orange' },
    { id: 13, top: '85%', left: '45%', delay: '3.2s', duration: '4s', color: 'star-lime' },
    { id: 14, top: '35%', left: '5%', delay: '1.8s', duration: '5s', color: 'star-fuchsia' },
    { id: 15, top: '55%', left: '35%', delay: '2.8s', duration: '4.5s', color: 'star-sky' },
])

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
}

const loadUserData = async () => {
    try {
        // Call /api/auth/me to get fresh user data with permissions
        const response = await authService.me()
        // authService.me() already updates localStorage with fresh user data
    } catch (error) {
        // If /me fails (token invalid/expired), logout and redirect to login
        console.error('Failed to load user data:', error)
        authService.clearAuth()
        router.push('/login')
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    checkMobile()
    window.addEventListener('resize', checkMobile)

    // Load user data on app initialization
    await loadUserData()
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
})
</script>
