<template>
    <!-- Mobile Overlay -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="appStore.sidebarOpen && isMobile"
            @click="appStore.toggleSidebar()"
            class="fixed inset-0 bg-black/40 bg-opacity-50 z-30 lg:hidden"
        ></div>
    </Transition>

    <!-- Sidebar -->
    <aside
        class="fixed top-0 h-full shadow-2xl transition-all duration-300 z-40 overflow-hidden opacity-90"
        :class="[
            appStore.sidebarOpen ? 'w-72' :  'w-24',
            isMobile && !appStore.sidebarOpen ? (appStore.direction === 'rtl' ? 'translate-x-full' : '-translate-x-full') : 'translate-x-0',
            appStore.darkMode
                ? 'bg-gradient-to-b from-gray-900/80 via-gray-800/80 to-gray-900/80 backdrop-blur-md'
                : 'bg-gradient-to-b from-primary/90 to-primary-dark/90 backdrop-blur-sm'
        ]"
    >
        <!-- Animated Background Icons -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-30">
            <!-- Pentool Icon - Purple Glow -->
            <div class="absolute floating-icon-1" style="top: 15%; left: 15%;">
                <svg class="w-8 h-8 text-purple-400 icon-glow-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>

            <!-- Circle Shape - Blue Glow -->
            <div class="absolute floating-icon-2" style="top: 35%; right: 10%;">
                <svg class="w-10 h-10 text-blue-400 icon-glow-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                </svg>
            </div>

            <!-- Triangle Shape - Pink Glow -->
            <div class="absolute floating-icon-3" style="top: 55%; left: 10%;">
                <svg class="w-9 h-9 text-pink-400 icon-glow-pink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l9 18H3L12 3z" />
                </svg>
            </div>

            <!-- Square Shape - Yellow Glow -->
            <div class="absolute floating-icon-4" style="top: 75%; right: 15%;">
                <svg class="w-8 h-8 text-yellow-400 icon-glow-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="1.5"/>
                </svg>
            </div>

            <!-- Bezier Curve - Cyan Glow -->
            <div class="absolute floating-icon-5" style="top: 25%; left: 20%;">
                <svg class="w-10 h-10 text-cyan-400 icon-glow-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 20 Q 8 4, 12 12 T 20 8" />
                    <circle cx="4" cy="20" r="1.5" fill="currentColor"/>
                    <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                    <circle cx="20" cy="8" r="1.5" fill="currentColor"/>
                </svg>
            </div>

            <!-- Layers Icon - Magenta Glow -->
            <div class="absolute floating-icon-6" style="top: 45%; right: 20%;">
                <svg class="w-9 h-9 text-fuchsia-400 icon-glow-magenta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
            </div>

            <!-- Brush Icon - Amber Glow -->
            <div class="absolute floating-icon-7" style="top: 65%; left: 25%;">
                <svg class="w-8 h-8 text-amber-400 icon-glow-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
            </div>

            <!-- Star Shape - Indigo Glow -->
            <div class="absolute floating-icon-8" style="top: 10%; right: 25%;">
                <svg class="w-7 h-7 text-indigo-400 icon-glow-indigo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>

            <!-- Diamond Shape - Yellow Glow -->
            <div class="absolute floating-icon-1" style="top: 42%; left: 45%; animation-delay: -2s;">
                <svg class="w-7 h-7 text-yellow-300 icon-glow-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l9.5 10L12 22 2.5 12z" />
                </svg>
            </div>

            <!-- Pentagon - Amber Glow -->
            <div class="absolute floating-icon-3" style="top: 85%; left: 50%; animation-delay: -5s;">
                <svg class="w-6 h-6 text-amber-300 icon-glow-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l7.5 5.5v9L12 22l-7.5-5.5v-9z" />
                </svg>
            </div>
        </div>
        <!-- Logo Section -->
        <div class="h-20 flex items-center justify-center px-4 border-b border-white/10 bg-black/10">
            <Transition
                mode="out-in"
                enter-active-class="transition-all duration-200"
                enter-from-class="opacity-0 scale-90"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition-all duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-90"
            >
                <div v-if="appStore.sidebarOpen" class="flex items-center justify-center w-full px-2">
                    <img
                        :src="logoIcon"
                        alt="Logo"
                        class="h-20 w-auto object-contain logo-glow-pulse"
                    />
                </div>
                <div v-else class="flex items-center justify-center">
                    <img
                        :src="logoIcon"
                        alt="Logo"
                        class="h-10 w-10 object-contain logo-glow-pulse"
                    />
                </div>
            </Transition>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-8rem)]">
            <template v-for="item in menuItems" :key="item.name">
                <!-- Menu Group -->
                <div v-if="item.children">
                    <!-- Group Header -->
                    <button
                        @click="toggleGroup(item.name)"
                        class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden text-white/80 hover:bg-white/10 hover:text-white"
                    >
                        <!-- Icon container -->
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-200 bg-white/5 text-white/70 group-hover:bg-white/10 group-hover:text-white">
                            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                        </div>

                        <!-- Label -->
                        <Transition
                            enter-active-class="transition-all duration-200"
                            enter-from-class="opacity-0 translate-x-2"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-active-class="transition-all duration-200"
                            leave-from-class="opacity-100 translate-x-0"
                            leave-to-class="opacity-0 translate-x-2"
                        >
                            <span v-if="appStore.sidebarOpen" class="font-semibold text-sm flex-1 text-start">{{ item.label }}</span>
                        </Transition>

                        <!-- Chevron -->
                        <svg
                            v-if="appStore.sidebarOpen"
                            class="w-4 h-4 transition-transform duration-200"
                            :class="openGroups.includes(item.name) ? 'rotate-180' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all duration-200"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="appStore.sidebarOpen && openGroups.includes(item.name)" class="mt-1 space-y-1 ps-4">
                            <router-link
                                v-for="child in item.children"
                                :key="child.name"
                                :to="child.route"
                                v-slot="{ isActive }"
                                @click="isMobile && appStore.toggleSidebar()"
                                class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 group relative"
                                :class="isActive
                                    ? 'bg-white/20 text-white shadow-lg backdrop-blur-sm'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'"
                            >
                                <div class="w-1.5 h-1.5 rounded-full" :class="isActive ? 'bg-accent' : 'bg-white/50'"></div>
                                <span class="text-sm">{{ child.label }}</span>
                                <span
                                    v-if="child.badge"
                                    class="ms-auto px-1.5 py-0.5 text-xs font-bold rounded-full bg-gradient-to-r from-accent to-accent-dark text-white shadow-lg"
                                >
                                    {{ child.badge }}
                                </span>
                            </router-link>
                        </div>
                    </Transition>
                </div>

                <!-- Single Menu Item -->
                <router-link
                    v-else
                    :to="item.route"
                    v-slot="{ isActive }"
                    @click="isMobile && appStore.toggleSidebar()"
                    class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden"
                    :class="isActive
                        ? 'bg-white/20 text-white shadow-lg backdrop-blur-sm'
                        : 'text-white/80 hover:bg-white/10 hover:text-white'"
                >
                    <!-- Active indicator -->
                    <div
                        v-if="isActive"
                        class="absolute start-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-gradient-to-b from-accent to-accent-dark rounded-e-full"
                    ></div>

                    <!-- Icon container -->
                    <div
                        class="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-200"
                        :class="isActive
                            ? 'bg-white/20 text-white'
                            : 'bg-white/5 text-white/70 group-hover:bg-white/10 group-hover:text-white'"
                    >
                        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    </div>

                    <!-- Label -->
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 translate-x-2"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition-all duration-200"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 translate-x-2"
                    >
                        <span v-if="appStore.sidebarOpen" class="font-semibold text-sm">{{ item.label }}</span>
                    </Transition>

                    <!-- Badge (optional) -->
                    <span
                        v-if="appStore.sidebarOpen && item.badge"
                        class="ms-auto px-2 py-1 text-xs font-bold rounded-full bg-gradient-to-r from-accent to-accent-dark text-white shadow-lg"
                    >
                        {{ item.badge }}
                    </span>
                </router-link>
            </template>
        </nav>

        <!-- Character Video (Above User Profile) -->
        <video 
            ref="profileVideo"
            class="absolute start-0 w-full h-32 object-contain pointer-events-none z-50 opacity-40"
            :class="appStore.sidebarOpen ? 'end-0' : 'end-0'"
            style="bottom: 90px;"
            muted
            playsinline
            @ended="onVideoEnded"
        >
            <source :src="settingDownVideo" type="video/mp4" />
        </video>

        <!-- User Profile (Bottom) -->
        <div class="absolute bottom-0 start-0 end-0 p-4 border-t border-white/10 bg-black/10">
            <div
                class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all cursor-pointer"
            >
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                    {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                </div>
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-all duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="appStore.sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ user?.name || 'User' }}</p>
                        <p class="text-xs text-white/70 truncate">{{ user?.email || '' }}</p>
                    </div>
                </Transition>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { h, ref, computed, onMounted, onUnmounted } from 'vue'
import { useAppStore } from '@/store'
import { useI18n } from 'vue-i18n'
import authService from '@/services/auth'
import logoFull from '@/assets/images/logo-full.png'
import logoIcon from '@/assets/images/logo-icon.png'
import settingDownVideo from '@/assets/videos/setting-down.mp4'

const appStore = useAppStore()
const { t } = useI18n()
const isMobile = ref(false)
const openGroups = ref(['management'])
const profileVideo = ref(null)

// Get current user
const user = computed(() => authService.getUser())

const isRTL = computed(() => { 
    console.log(appStore.direction)
    return appStore.direction === 'rtl'
})

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
    if (!isMobile.value && !appStore.sidebarOpen) {
        appStore.sidebarOpen = false
    }
}

const toggleGroup = (groupName) => {
    const index = openGroups.value.indexOf(groupName)
    if (index > -1) {
        openGroups.value.splice(index, 1)
    } else {
        openGroups.value.push(groupName)
    }
}

// Video playback handlers
const playProfileVideo = () => {
    if (profileVideo.value) {
        profileVideo.value.play().catch(error => {
            // Autoplay might be blocked by browser, silently handle
            console.log('Video autoplay prevented:', error)
        })
    }
}

const onVideoEnded = () => {
    // Video ended, do nothing (no loop as requested)
}

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
    // Play the video when component mounts
    playProfileVideo()
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
})

// Check if user has permission
const hasPermission = (permission) => {
    return authService.hasPermission(permission)
}

// Check if user has role
const hasRole = (role) => {
    return authService.hasRole(role)
}

// Client Menu Items (visible to all authenticated users)
const clientMenuItems = computed(() => [
    {
        name: 'dashboard',
        label: t('sidebar.dashboard'),
        route: '/admin/dashboard',
        badge: null,
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
        ]),

    },
    {
        name: 'ai-image-generator',
        label: t('sidebar.ai_image_generator'),
        route: '/client/ai-image-generator',
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' })
        ]),
    },
    {
        name: 'products',
        label: t('sidebar.products'),
        route: '/client/products',
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })
        ]),
    },
    {
        name: 'ai-generations',
        label: t('sidebar.ai_generations'),
        route: '/client/ai-generations',
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]),
    }
])

// Admin Menu Items (visible only to users with specific permissions)
const adminMenuItems = computed(() => {
    const items = []

    // Management Group (only if user has any management permissions)
    const managementChildren = []

    if (hasPermission('users.view')) {
        managementChildren.push({
            name: 'users',
            label: t('sidebar.users'),
            route: '/admin/users',
            permission: 'users.view'
        })
    }

    if (hasPermission('roles.view')) {
        managementChildren.push({
            name: 'roles',
            label: t('sidebar.roles'),
            route: '/admin/roles',
            permission: 'roles.view'
        })
    }

    if (hasPermission('settings.view')) {
        managementChildren.push({
            name: 'settings',
            label: t('sidebar.settings'),
            route: '/admin/settings',
            permission: 'settings.view'
        })
    }

    if (managementChildren.length > 0) {
        items.push({
            name: 'management',
            label: t('sidebar.management'),
            icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' })
            ]),
            children: managementChildren
        })
    }

   

    return items
})

// Combined menu items (client + admin)
const menuItems = computed(() => {
    return [...clientMenuItems.value, ...adminMenuItems.value]
})
</script>

<style scoped>
/* Logo Glow Pulse Effect */
.logo-glow-pulse {
    filter: drop-shadow(0 0 8px rgba(147, 51, 234, 0.3));
    animation: logo-pulse 3s ease-in-out infinite;
}

@keyframes logo-pulse {
    0%, 100% {
        filter: drop-shadow(0 0 2px rgba(147, 51, 234, 0.3));
    }
    50% {
        filter: drop-shadow(0 0 10px rgba(0, 57, 100, 1));
    }
}

/* Colorful Icon Glow Effects */
.icon-glow-purple {
    filter: drop-shadow(0 0 6px rgba(168, 85, 247, 0.8))
            drop-shadow(0 0 12px rgba(168, 85, 247, 0.6))
            drop-shadow(0 0 18px rgba(168, 85, 247, 0.4));
    animation: glow-pulse-purple 3s ease-in-out infinite;
}

.icon-glow-blue {
    filter: drop-shadow(0 0 6px rgba(96, 165, 250, 0.8))
            drop-shadow(0 0 12px rgba(96, 165, 250, 0.6))
            drop-shadow(0 0 18px rgba(96, 165, 250, 0.4));
    animation: glow-pulse-blue 3.5s ease-in-out infinite;
}

.icon-glow-pink {
    filter: drop-shadow(0 0 6px rgba(244, 114, 182, 0.8))
            drop-shadow(0 0 12px rgba(244, 114, 182, 0.6))
            drop-shadow(0 0 18px rgba(244, 114, 182, 0.4));
    animation: glow-pulse-pink 3.2s ease-in-out infinite;
}

.icon-glow-yellow {
    filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.9))
            drop-shadow(0 0 12px rgba(251, 191, 36, 0.7))
            drop-shadow(0 0 20px rgba(251, 191, 36, 0.5));
    animation: glow-pulse-yellow 2.8s ease-in-out infinite;
}

.icon-glow-cyan {
    filter: drop-shadow(0 0 6px rgba(34, 211, 238, 0.8))
            drop-shadow(0 0 12px rgba(34, 211, 238, 0.6))
            drop-shadow(0 0 18px rgba(34, 211, 238, 0.4));
    animation: glow-pulse-cyan 3.3s ease-in-out infinite;
}

.icon-glow-magenta {
    filter: drop-shadow(0 0 6px rgba(217, 70, 239, 0.8))
            drop-shadow(0 0 12px rgba(217, 70, 239, 0.6))
            drop-shadow(0 0 18px rgba(217, 70, 239, 0.4));
    animation: glow-pulse-magenta 3.6s ease-in-out infinite;
}

.icon-glow-amber {
    filter: drop-shadow(0 0 6px rgba(251, 146, 60, 0.9))
            drop-shadow(0 0 12px rgba(251, 146, 60, 0.7))
            drop-shadow(0 0 20px rgba(251, 146, 60, 0.5));
    animation: glow-pulse-amber 2.9s ease-in-out infinite;
}

.icon-glow-indigo {
    filter: drop-shadow(0 0 6px rgba(129, 140, 248, 0.8))
            drop-shadow(0 0 12px rgba(129, 140, 248, 0.6))
            drop-shadow(0 0 18px rgba(129, 140, 248, 0.4));
    animation: glow-pulse-indigo 3.4s ease-in-out infinite;
}

/* Individual Glow Pulse Animations */
@keyframes glow-pulse-purple {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-blue {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-pink {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-yellow {
    0%, 100% { opacity: 0.9; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-cyan {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-magenta {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-amber {
    0%, 100% { opacity: 0.9; }
    50% { opacity: 1; }
}

@keyframes glow-pulse-indigo {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

/* Floating animations for graphic design icons */
@keyframes float-1 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(10px, -15px) rotate(5deg);
    }
    50% {
        transform: translate(-5px, -25px) rotate(-3deg);
    }
    75% {
        transform: translate(-15px, -10px) rotate(7deg);
    }
}

@keyframes float-2 {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(-12px, 20px) scale(1.1);
    }
    66% {
        transform: translate(15px, -15px) scale(0.95);
    }
}

@keyframes float-3 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    30% {
        transform: translate(18px, 12px) rotate(-10deg);
    }
    60% {
        transform: translate(-10px, 25px) rotate(8deg);
    }
}

@keyframes float-4 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg) scale(1);
    }
    25% {
        transform: translate(-15px, -20px) rotate(15deg) scale(1.05);
    }
    50% {
        transform: translate(10px, -10px) rotate(-12deg) scale(0.95);
    }
    75% {
        transform: translate(20px, 15px) rotate(8deg) scale(1.1);
    }
}

@keyframes float-5 {
    0%, 100% {
        transform: translate(0, 0);
    }
    40% {
        transform: translate(-20px, -18px);
    }
    80% {
        transform: translate(15px, 22px);
    }
}

@keyframes float-6 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    35% {
        transform: translate(12px, -25px) rotate(-15deg);
    }
    70% {
        transform: translate(-18px, 10px) rotate(12deg);
    }
}

@keyframes float-7 {
    0%, 100% {
        transform: translate(0, 0) scale(1) rotate(0deg);
    }
    25% {
        transform: translate(15px, 18px) scale(1.08) rotate(5deg);
    }
    50% {
        transform: translate(-12px, -20px) scale(0.92) rotate(-8deg);
    }
    75% {
        transform: translate(-20px, 15px) scale(1.05) rotate(10deg);
    }
}

@keyframes float-8 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    33% {
        transform: translate(-15px, 20px) rotate(180deg);
    }
    66% {
        transform: translate(20px, -15px) rotate(360deg);
    }
}

/* Apply animations to each icon with different durations and delays */
.floating-icon-1 {
    animation: float-1 8s ease-in-out infinite;
}

.floating-icon-2 {
    animation: float-2 10s ease-in-out infinite;
    animation-delay: -2s;
}

.floating-icon-3 {
    animation: float-3 9s ease-in-out infinite;
    animation-delay: -4s;
}

.floating-icon-4 {
    animation: float-4 11s ease-in-out infinite;
    animation-delay: -1s;
}

.floating-icon-5 {
    animation: float-5 7s ease-in-out infinite;
    animation-delay: -3s;
}

.floating-icon-6 {
    animation: float-6 12s ease-in-out infinite;
    animation-delay: -5s;
}

.floating-icon-7 {
    animation: float-7 9.5s ease-in-out infinite;
    animation-delay: -2.5s;
}

.floating-icon-8 {
    animation: float-8 13s ease-in-out infinite;
    animation-delay: -6s;
}
</style>