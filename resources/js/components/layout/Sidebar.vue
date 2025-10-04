<template>
    <aside
        class="fixed top-0 start-0 h-full bg-white border-e border-gray-200 transition-all duration-300 z-40"
        :class="appStore.sidebarOpen ? 'w-64' : 'w-20'"
    >
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            <h1 class="text-xl font-bold text-primary-600" v-if="appStore.sidebarOpen">
                Dashboard
            </h1>
            <span class="text-2xl font-bold text-primary-600" v-else>D</span>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <router-link
                v-for="item in menuItems"
                :key="item.name"
                :to="item.route"
                v-slot="{ isActive }"
                class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
                :class="isActive
                    ? 'bg-primary-50 text-primary-600'
                    : 'text-gray-700 hover:bg-gray-100'"
            >
                <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                <span v-if="appStore.sidebarOpen" class="font-medium">{{ item.label }}</span>
            </router-link>
        </nav>
    </aside>
</template>

<script setup>
import { h } from 'vue'
import { useAppStore } from '@/store'

const appStore = useAppStore()

const menuItems = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        route: '/',
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
        ])
    },
    {
        name: 'users',
        label: 'Users',
        route: '/users',
        icon: () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
        ])
    }
]
</script>
