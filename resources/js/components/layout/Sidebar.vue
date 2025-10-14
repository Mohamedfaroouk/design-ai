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
        class="fixed top-0 h-full shadow-2xl transition-all duration-300 z-40"
        :class="[
            appStore.sidebarOpen ? 'w-72' :  'w-24',
            isMobile && !appStore.sidebarOpen ? (appStore.direction === 'rtl' ? 'translate-x-full' : '-translate-x-full') : 'translate-x-0',
            appStore.darkMode
                ? 'bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900'
                : 'bg-gradient-to-b from-primary via-secondary to-primary-dark'
        ]"
    >
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
                <div v-if="appStore.sidebarOpen" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Dashboard</h1>
                        <p class="text-xs text-white/70">Admin Panel</p>
                    </div>
                </div>
                <div v-else class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
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

const appStore = useAppStore()
const { t } = useI18n()
const isMobile = ref(false)
const openGroups = ref(['management'])

// Get current user
const user = computed(() => authService.getUser())

const isRTL = computed(() => { 
    console.log(appStore.direction)
    return appStore.direction === 'rtl'
})

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
    if (!isMobile.value && !appStore.sidebarOpen) {
        appStore.sidebarOpen = true
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

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
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