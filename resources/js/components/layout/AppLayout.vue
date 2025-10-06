<template>
    <div class="min-h-screen transition-colors duration-300"
         :class="appStore.darkMode
             ? 'bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950'
             : 'bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30'">

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
                class="transition-all duration-300 min-h-screen"
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
