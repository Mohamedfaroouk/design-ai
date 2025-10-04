<template>
    <header
        class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 transition-all duration-300"
        :class="appStore.sidebarOpen ? 'ms-64' : 'ms-20'"
    >
        <div class="flex items-center gap-4">
            <!-- Toggle Sidebar -->
            <button
                @click="appStore.toggleSidebar()"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <!-- Breadcrumb or Title -->
            <h2 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h2>
        </div>

        <div class="flex items-center gap-4">
            <!-- RTL/LTR Toggle -->
            <button
                @click="toggleDirection"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                title="Toggle Direction"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                    />
                </svg>
            </button>

            <!-- Notifications -->
            <button class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>
                <span class="absolute top-1 end-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Menu -->
            <div class="relative">
                <button
                    @click="showUserMenu = !showUserMenu"
                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center">
                        <span class="text-sm font-medium">U</span>
                    </div>
                    <span class="text-sm font-medium text-gray-700">User</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <!-- User Dropdown -->
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition-all duration-200"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="showUserMenu"
                        class="absolute end-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1"
                    >
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Settings
                        </a>
                        <hr class="my-1 border-gray-200" />
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            Logout
                        </a>
                    </div>
                </Transition>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '@/store'

const appStore = useAppStore()
const route = useRoute()
const showUserMenu = ref(false)

const pageTitle = computed(() => route.meta.title || 'Dashboard')

const toggleDirection = () => {
    const newDir = appStore.direction === 'ltr' ? 'rtl' : 'ltr'
    appStore.setDirection(newDir)
}

// Close user menu when clicking outside
const closeUserMenu = (event) => {
    if (!event.target.closest('.relative')) {
        showUserMenu.value = false
    }
}

document.addEventListener('click', closeUserMenu)
</script>
