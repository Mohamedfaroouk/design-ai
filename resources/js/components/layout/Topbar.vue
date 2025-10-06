<template>
    <header
        class="fixed top-0 end-0 h-20 border-b flex items-center justify-between px-4 sm:px-6 transition-all duration-300 shadow-sm z-20 backdrop-blur-sm"
        :class="[
            appStore.sidebarOpen && !isMobile ? 'start-72' : 'start-0',
            !appStore.sidebarOpen && !isMobile ? 'start-20' : '',
            appStore.darkMode
                ? 'bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 border-gray-700'
                : 'bg-gradient-to-r from-white via-primary-50 to-secondary-50 border-primary-100'
        ]"
    >
        <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
            <!-- Hamburger Menu (Mobile) -->
            <button
                @click="appStore.toggleSidebar()"
                class="p-2.5 rounded-xl transition-all duration-200 group lg:hidden"
                :class="appStore.darkMode ? 'hover:bg-gray-700' : 'hover:bg-primary-100'"
            >
                <svg class="w-6 h-6 transition-colors"
                     :class="appStore.darkMode
                         ? 'text-gray-300 group-hover:text-primary-400'
                         : 'text-gray-700 group-hover:text-primary-600'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <!-- Toggle Sidebar (Desktop) -->
            <button
                @click="appStore.toggleSidebar()"
                class="hidden lg:flex p-2.5 rounded-xl bg-gradient-to-br from-primary to-secondary hover:from-primary-dark hover:to-secondary-dark transition-all duration-200 shadow-lg hover:shadow-xl group"
            >
                <svg class="w-5 h-5 text-white transition-transform duration-200" :class="appStore.sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                    />
                </svg>
            </button>

            <!-- Page Title with Icon -->
            <div class="flex items-center gap-3 min-w-0">
                <div class="hidden sm:flex w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary items-center justify-center shadow-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent truncate">
                        {{ pageTitle }}
                    </h2>
                    <p class="text-xs hidden sm:block"
                       :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                        {{ $t('topbar.welcomeBack') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Search (Desktop) -->
            <button class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl transition-all group"
                    :class="appStore.darkMode
                        ? 'bg-gray-800 border border-gray-700 text-gray-400 hover:text-gray-200'
                        : 'bg-white border border-primary-200 hover:border-primary-300 text-gray-400 hover:text-gray-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-sm">{{ $t('common.search') }}</span>
                <kbd class="hidden lg:inline-block px-2 py-0.5 text-xs rounded"
                     :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-100'">⌘K</kbd>
            </button>

            <!-- Dark Mode Toggle -->
            <DarkModeToggle />

            <!-- Language Switcher -->
            <LanguageSwitcher />

            <!-- Notifications -->
            <button class="relative p-2.5 rounded-xl transition-all group"
                    :class="appStore.darkMode
                        ? 'bg-gray-800 border border-gray-700 hover:bg-gray-700'
                        : 'bg-white border border-primary-200 hover:border-primary-300 hover:bg-primary-50'">
                <svg class="w-5 h-5 transition-colors"
                     :class="appStore.darkMode
                         ? 'text-gray-400 group-hover:text-gray-200'
                         : 'text-gray-600 group-hover:text-primary-600'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>
                <span class="absolute -top-1 -end-1 w-5 h-5 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-lg">
                    3
                </span>
            </button>

            <!-- User Menu -->
            <div class="relative">
                <button
                    @click="showUserMenu = !showUserMenu"
                    class="flex items-center gap-2 p-1.5 sm:px-3 sm:py-2 rounded-xl border hover:shadow-md transition-all group"
                    :class="appStore.darkMode
                        ? 'bg-gray-800 border-gray-700 hover:border-gray-600'
                        : 'bg-white border-primary-200 hover:border-primary-300'"
                >
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-gradient-to-br from-secondary to-secondary-dark flex items-center justify-center shadow-lg flex-shrink-0">
                        <span class="text-sm font-bold text-white">{{ userInitials }}</span>
                    </div>
                    <div class="hidden sm:block text-start">
                        <p class="text-sm font-semibold"
                           :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-800'">
                            {{ currentUser?.name || 'User' }}
                        </p>
                        <p class="text-xs"
                           :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                            {{ currentUser?.roles?.[0]?.name || 'User' }}
                        </p>
                    </div>
                    <svg class="hidden sm:block w-4 h-4 transition-transform duration-200"
                         :class="[
                             showUserMenu ? 'rotate-180' : '',
                             appStore.darkMode
                                 ? 'text-gray-400 group-hover:text-primary-400'
                                 : 'text-gray-400 group-hover:text-primary-600'
                         ]"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    enter-from-class="opacity-0 scale-95 translate-y-2"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="showUserMenu"
                        class="absolute end-0 mt-2 w-56 rounded-2xl shadow-2xl border py-2 z-50"
                        :class="appStore.darkMode
                            ? 'bg-gray-800 border-gray-700'
                            : 'bg-white border-primary-100'"
                    >
                        <div class="px-4 py-3 border-b"
                             :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-100'">
                            <p class="text-sm font-semibold"
                               :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-800'">
                                {{ currentUser?.name || 'User' }}
                            </p>
                            <p class="text-xs"
                               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                                {{ currentUser?.email || '' }}
                            </p>
                        </div>

                        <router-link to="/admin/profile" @click="showUserMenu = false" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors group"
                           :class="appStore.darkMode
                               ? 'text-gray-300 hover:bg-gray-700'
                               : 'text-gray-700 hover:bg-primary-50'">
                            <svg class="w-5 h-5 transition-colors"
                                 :class="appStore.darkMode
                                     ? 'text-gray-400 group-hover:text-primary-400'
                                     : 'text-gray-400 group-hover:text-primary-600'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $t('topbar.profile') }}
                        </router-link>
                        <router-link v-if="hasPermission('settings.view')" to="/admin/settings" @click="showUserMenu = false" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors group"
                           :class="appStore.darkMode
                               ? 'text-gray-300 hover:bg-gray-700'
                               : 'text-gray-700 hover:bg-primary-50'">
                            <svg class="w-5 h-5 transition-colors"
                                 :class="appStore.darkMode
                                     ? 'text-gray-400 group-hover:text-primary-400'
                                     : 'text-gray-400 group-hover:text-primary-600'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $t('topbar.settings') }}
                        </router-link>

                        <hr class="my-2"
                            :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-100'" />

                        <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ $t('topbar.logout') }}
                        </button>
                    </div>
                </Transition>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import authService from '@/services/auth'
import LanguageSwitcher from '@/components/ui/LanguageSwitcher.vue'
import DarkModeToggle from '@/components/ui/DarkModeToggle.vue'

const appStore = useAppStore()
const toast = useToastStore()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const showUserMenu = ref(false)
const isMobile = ref(false)

const pageTitle = computed(() => route.meta.title || 'Dashboard')
const currentUser = computed(() => authService.getUser())
const userInitials = computed(() => {
    const name = currentUser.value?.name || 'U'
    return name.charAt(0).toUpperCase()
})

// Check if user has permission
const hasPermission = (permission) => {
    return authService.hasPermission(permission)
}

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
}

const handleLogout = async () => {
    try {
        await authService.logout()
        toast.success(t('auth.logout') + ' ' + t('auth.login.success'))
        router.push('/login')
    } catch (error) {
        toast.error(error.message || t('common.error'))
    }
}

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
    document.addEventListener('click', closeUserMenu)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
    document.removeEventListener('click', closeUserMenu)
})

// Close user menu when clicking outside
const closeUserMenu = (event) => {
    if (!event.target.closest('.relative')) {
        showUserMenu.value = false
    }
}
</script>
