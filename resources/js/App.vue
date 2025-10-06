<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'

const { locale } = useI18n()
const appStore = useAppStore()

onMounted(() => {
    // Get saved locale from localStorage or use default
    const savedLocale = localStorage.getItem('locale') || 'en'

    // Set the locale
    locale.value = savedLocale

    // Set direction based on locale
    const direction = savedLocale === 'ar' ? 'rtl' : 'ltr'
    appStore.setDirection(direction)

    // Apply direction to document
    document.documentElement.setAttribute('dir', direction)
})
</script>
