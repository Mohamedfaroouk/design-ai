<template>
    <button
        @click="toggleDarkMode"
        class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
        :class="appStore.darkMode
            ? 'bg-gray-800 hover:bg-gray-700 text-gray-200'
            : 'bg-white hover:bg-gray-50 text-gray-700'"
        :title="appStore.darkMode ? $t('darkMode.light') : $t('darkMode.dark')"
    >
        <Transition
            mode="out-in"
            enter-active-class="transition-all duration-200"
            enter-from-class="opacity-0 rotate-90 scale-50"
            enter-to-class="opacity-100 rotate-0 scale-100"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 rotate-0 scale-100"
            leave-to-class="opacity-0 -rotate-90 scale-50"
        >
            <!-- Sun Icon (Light Mode) -->
            <svg
                v-if="!appStore.darkMode"
                key="sun"
                class="w-5 h-5 text-yellow-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                />
            </svg>
            <!-- Moon Icon (Dark Mode) -->
            <svg
                v-else
                key="moon"
                class="w-5 h-5 text-indigo-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                />
            </svg>
        </Transition>
    </button>
</template>

<script setup>
import { useAppStore } from '@/store'

const appStore = useAppStore()

const toggleDarkMode = () => {
    appStore.toggleDarkMode()
}
</script>
