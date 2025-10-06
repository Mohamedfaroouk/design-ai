<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="isOpen = !isOpen"
            class="flex items-center gap-2 px-3 py-2 border-2 rounded-xl text-sm font-medium transition-all duration-200"
            :class="appStore.darkMode
                ? 'bg-gray-800 border-gray-600 text-gray-200 hover:bg-gray-700 hover:border-gray-500'
                : 'bg-white border-primary-200 text-gray-700 hover:bg-primary-50 hover:border-primary-300'"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
            <span class="hidden sm:inline">{{ currentLanguage.label }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition-all duration-200"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute end-0 mt-2 w-48 rounded-2xl shadow-2xl overflow-hidden z-50 border"
                :class="appStore.darkMode
                    ? 'bg-gray-800 border-gray-700'
                    : 'bg-white border-primary-100'"
            >
                <button
                    v-for="lang in languages"
                    :key="lang.code"
                    @click="changeLanguage(lang.code)"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm transition-colors"
                    :class="[
                        locale === lang.code
                            ? appStore.darkMode
                                ? 'bg-primary-900/30 text-primary-400 font-semibold'
                                : 'bg-primary-50 text-primary-700 font-semibold'
                            : appStore.darkMode
                                ? 'text-gray-300 hover:bg-gray-700 hover:text-gray-200'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    ]"
                >
                    <span class="text-2xl">{{ lang.flag }}</span>
                    <span>{{ lang.label }}</span>
                    <svg v-if="locale === lang.code" class="w-5 h-5 ms-auto" :class="appStore.darkMode ? 'text-primary-400' : 'text-primary-600'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'

const { locale } = useI18n()
const appStore = useAppStore()
const isOpen = ref(false)
const dropdownRef = ref(null)

const languages = [
    { code: 'en', label: 'English', flag: '🇬🇧' },
    { code: 'ar', label: 'العربية', flag: '🇸🇦' }
]

const currentLanguage = computed(() => {
    return languages.find(lang => lang.code === locale.value) || languages[0]
})

const changeLanguage = (code) => {
    locale.value = code
    localStorage.setItem('locale', code)

    // Update direction based on language
    const direction = code === 'ar' ? 'rtl' : 'ltr'
    appStore.setDirection(direction)

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
