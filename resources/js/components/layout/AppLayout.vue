<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main Content Area -->
        <div
            class="transition-all duration-300"
            :class="appStore.sidebarOpen ? 'ms-64' : 'ms-20'"
        >
            <!-- Topbar -->
            <Topbar />

            <!-- Page Content -->
            <main class="p-6">
                <router-view v-slot="{ Component }">
                    <transition
                        enter-active-class="transition-all duration-300"
                        enter-from-class="opacity-0 translate-y-4"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition-all duration-300"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-4"
                        mode="out-in"
                    >
                        <component :is="Component" />
                    </transition>
                </router-view>
            </main>
        </div>

        <!-- Toast Notifications -->
        <Toast />
    </div>
</template>

<script setup>
import { useAppStore } from '@/store'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'
import Toast from '@/components/ui/Toast.vue'

const appStore = useAppStore()
</script>
