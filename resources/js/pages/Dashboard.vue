<template>
    <div v-motion-fade>
        <h1 class="text-3xl font-bold mb-6 transition-colors"
            :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
            Dashboard
        </h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                v-for="(stat, index) in stats"
                :key="index"
                v-motion
                :initial="{ opacity: 0, y: 20 }"
                :enter="{ opacity: 1, y: 0, transition: { delay: index * 100 } }"
                class="rounded-lg shadow p-6 transition-colors"
                :class="appStore.darkMode ? 'bg-gray-800' : 'bg-white'"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium transition-colors"
                           :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                            {{ stat.label }}
                        </p>
                        <p class="text-2xl font-bold mt-2 transition-colors"
                           :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                            {{ stat.value }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full flex items-center justify-center"
                        :class="stat.bgColor"
                    >
                        <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span
                        class="font-medium"
                        :class="stat.trend === 'up'
                            ? (appStore.darkMode ? 'text-green-400' : 'text-green-600')
                            : (appStore.darkMode ? 'text-red-400' : 'text-red-600')"
                    >
                        {{ stat.change }}
                    </span>
                    <span class="ms-2 transition-colors"
                          :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                        from last month
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts/Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="rounded-lg shadow p-6 transition-colors"
                 :class="appStore.darkMode ? 'bg-gray-800' : 'bg-white'">
                <h2 class="text-lg font-semibold mb-4 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    Recent Activity
                </h2>
                <div class="space-y-4">
                    <div
                        v-for="(activity, index) in recentActivity"
                        :key="index"
                        class="flex items-start gap-4 pb-4 border-b last:border-0 transition-colors"
                        :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'"
                    >
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                            :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-100'"
                        >
                            <span class="text-sm font-medium transition-colors"
                                  :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                                {{ activity.user.charAt(0) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium transition-colors"
                               :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-900'">
                                {{ activity.user }}
                            </p>
                            <p class="text-sm transition-colors"
                               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                                {{ activity.action }}
                            </p>
                            <p class="text-xs mt-1 transition-colors"
                               :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-500'">
                                {{ activity.time }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-lg shadow p-6 transition-colors"
                 :class="appStore.darkMode ? 'bg-gray-800' : 'bg-white'">
                <h2 class="text-lg font-semibold mb-4 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    Quick Actions
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <Button
                        variant="primary"
                        @click="$router.push('/users/create')"
                        class="justify-center"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Add User
                    </Button>
                    <Button variant="secondary" class="justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Generate Report
                    </Button>
                    <Button variant="secondary" class="justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>
                        Settings
                    </Button>
                    <Button variant="secondary" class="justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Help
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { h } from 'vue'
import { useAppStore } from '@/store'
import Button from '@/components/ui/Button.vue'

const appStore = useAppStore()

const stats = [
    {
        label: 'Total Users',
        value: '1,234',
        change: '+12%',
        trend: 'up',
        bgColor: 'bg-blue-100',
        iconColor: 'text-blue-600',
        icon: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
        ])
    },
    {
        label: 'Revenue',
        value: '$45,231',
        change: '+23%',
        trend: 'up',
        bgColor: 'bg-green-100',
        iconColor: 'text-green-600',
        icon: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
        ])
    },
    {
        label: 'Active Projects',
        value: '47',
        change: '+5%',
        trend: 'up',
        bgColor: 'bg-purple-100',
        iconColor: 'text-purple-600',
        icon: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    },
    {
        label: 'Tasks Completed',
        value: '892',
        change: '-3%',
        trend: 'down',
        bgColor: 'bg-yellow-100',
        iconColor: 'text-yellow-600',
        icon: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
        ])
    }
]

const recentActivity = [
    { user: 'John Doe', action: 'Created a new user', time: '2 minutes ago' },
    { user: 'Jane Smith', action: 'Updated project settings', time: '1 hour ago' },
    { user: 'Mike Johnson', action: 'Completed task "Design Review"', time: '3 hours ago' },
    { user: 'Sarah Williams', action: 'Uploaded new documents', time: '5 hours ago' },
    { user: 'Tom Brown', action: 'Added comment on ticket #123', time: '1 day ago' }
]
</script>
