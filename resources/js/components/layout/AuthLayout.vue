<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 transition-colors duration-200"
         :class="appStore.darkMode ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900' : 'bg-gradient-to-br from-gray-50 via-white to-gray-100'">
        <!-- Background Decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Top Right Circle -->
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-20"
                 :class="appStore.darkMode ? 'bg-primary-500' : 'bg-primary-200'"></div>
            <!-- Bottom Left Circle -->
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full opacity-10"
                 :class="appStore.darkMode ? 'bg-secondary-500' : 'bg-secondary-200'"></div>
            <!-- Floating Shapes -->
            <div class="absolute top-1/4 right-1/4 w-32 h-32 rounded-full opacity-10 blur-xl"
                 :class="appStore.darkMode ? 'bg-accent-500' : 'bg-accent-200'"></div>
        </div>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <!-- Logo/Brand -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-200"
                         :class="appStore.darkMode ? 'bg-gradient-to-br from-primary-600 to-primary-700' : 'bg-gradient-to-br from-primary-500 to-primary-600'">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold tracking-tight transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ title }}
                </h2>
                <p v-if="subtitle" class="mt-2 text-sm transition-colors"
                   :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                    {{ subtitle }}
                </p>
            </div>

            <!-- Card Container -->
            <div class="rounded-2xl shadow-2xl p-8 backdrop-blur-sm transition-all duration-200"
                 :class="appStore.darkMode
                     ? 'bg-gray-800/50 border border-gray-700'
                     : 'bg-white/80 border border-gray-200'">
                <!-- Slot for page content -->
                <slot />
            </div>

            <!-- Language & Theme Toggles -->
            <div class="flex items-center justify-center gap-4">
                <LanguageSwitcher />
                <DarkModeToggle />
            </div>

            <!-- Footer Links -->
            <div v-if="$slots.footer" class="text-center">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAppStore } from '@/store'
import LanguageSwitcher from '@/components/ui/LanguageSwitcher.vue'
import DarkModeToggle from '@/components/ui/DarkModeToggle.vue'

const appStore = useAppStore()

defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    }
})
</script>
