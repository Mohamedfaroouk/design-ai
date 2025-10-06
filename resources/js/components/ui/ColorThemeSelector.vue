<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="isOpen = !isOpen"
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
            :class="appStore.darkMode
                ? 'bg-gray-800 hover:bg-gray-700 text-gray-200'
                : 'bg-white hover:bg-gray-50 text-gray-700'"
        >
            <div class="w-5 h-5 rounded-full" :class="`bg-${appStore.primaryColor}-500`"></div>
            <span class="hidden sm:inline">{{ $t('theme.label') }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition-all duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute end-0 mt-2 w-56 rounded-xl shadow-2xl overflow-hidden z-50 border-2"
                :class="appStore.darkMode
                    ? 'bg-gray-800 border-gray-700'
                    : 'bg-white border-gray-200'"
            >
                <div class="p-3">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-3"
                       :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                        {{ $t('theme.label') }}
                    </p>
                    <div class="grid grid-cols-7 gap-2">
                        <button
                            v-for="(theme, key) in themes"
                            :key="key"
                            @click="selectTheme(key)"
                            class="w-8 h-8 rounded-full transition-all duration-200 relative group"
                            :class="[
                                `bg-${key}-500 hover:scale-110`,
                                appStore.primaryColor === key ? 'ring-2 ring-offset-2 scale-110' : ''
                            ]"
                            :style="{ backgroundColor: getThemeColor(key) }"
                            :title="$t(`theme.${key}`)"
                        >
                            <div v-if="appStore.primaryColor === key" class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAppStore } from '@/store'
import { colorThemes } from '@/config/themes'

const appStore = useAppStore()
const isOpen = ref(false)
const dropdownRef = ref(null)

const themes = colorThemes

const themeColors = {
    indigo: '#6366f1',
    blue: '#3b82f6',
    purple: '#a855f7',
    pink: '#ec4899',
    green: '#10b981',
    orange: '#f97316',
    red: '#ef4444'
}

const getThemeColor = (theme) => {
    return themeColors[theme] || themeColors.indigo
}

const selectTheme = (theme) => {
    appStore.setPrimaryColor(theme)
    isOpen.value = false
}

const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown)
})
</script>
