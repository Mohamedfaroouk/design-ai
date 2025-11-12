<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 transition-colors duration-200 relative overflow-hidden"
         :class="appStore.darkMode ? 'galaxy-bg' : 'bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30'">

        <!-- Shooting Stars (Dark Mode Only) -->
        <template v-if="appStore.darkMode">
            <div class="star-field"></div>
            <div
                v-for="star in shootingStars"
                :key="star.id"
                class="shooting-star"
                :style="{
                    top: star.top,
                    left: star.left,
                    animationDelay: star.delay,
                    animationDuration: star.duration
                }"
            ></div>
        </template>

        <!-- Magic Waving Stars (Light Mode Only) -->
        <template v-else>
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div
                    v-for="star in magicStars"
                    :key="star.id"
                    class="magic-star"
                    :class="star.color"
                    :style="{
                        top: star.top,
                        left: star.left,
                        animationDelay: star.delay,
                        animationDuration: star.duration
                    }"
                >
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2L12 16.8l-6.4 4.4 2.4-7.2-6-4.8h7.6z" />
                    </svg>
                </div>
            </div>
        </template>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <!-- Logo/Brand -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <img
                        :src="logoIcon"
                        alt="Logo"
                        class="h-20 w-20 object-contain logo-glow-pulse"
                    />
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
            <div class="rounded-2xl shadow-2xl p-8 backdrop-blur-lg transition-all duration-200"
                 :class="appStore.darkMode
                     ? 'bg-gray-800/40 border border-gray-700/50'
                     : 'bg-white/30 border border-primary-100/50'">
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
import { ref } from 'vue'
import { useAppStore } from '@/store'
import LanguageSwitcher from '@/components/ui/LanguageSwitcher.vue'
import DarkModeToggle from '@/components/ui/DarkModeToggle.vue'
import logoIcon from '@/assets/images/logo-icon.png'

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

// Generate random shooting stars for dark mode
const shootingStars = ref([
    { id: 1, top: '10%', left: '20%', delay: '0s', duration: '3s' },
    { id: 2, top: '30%', left: '60%', delay: '2s', duration: '3.5s' },
    { id: 3, top: '50%', left: '40%', delay: '4s', duration: '3s' },
    { id: 4, top: '20%', left: '80%', delay: '6s', duration: '4s' },
    { id: 5, top: '70%', left: '10%', delay: '8s', duration: '3s' },
    { id: 6, top: '40%', left: '70%', delay: '10s', duration: '3.5s' },
])

// Generate magic colorful waving stars for light mode
const magicStars = ref([
    { id: 1, top: '10%', left: '15%', delay: '0s', duration: '4s', color: 'star-purple' },
    { id: 2, top: '25%', left: '75%', delay: '0.5s', duration: '5s', color: 'star-pink' },
    { id: 3, top: '40%', left: '20%', delay: '1s', duration: '4.5s', color: 'star-blue' },
    { id: 4, top: '15%', left: '85%', delay: '1.5s', duration: '5.5s', color: 'star-yellow' },
    { id: 5, top: '60%', left: '10%', delay: '2s', duration: '4s', color: 'star-cyan' },
    { id: 6, top: '75%', left: '60%', delay: '2.5s', duration: '5s', color: 'star-magenta' },
    { id: 7, top: '50%', left: '90%', delay: '3s', duration: '4.5s', color: 'star-amber' },
    { id: 8, top: '30%', left: '40%', delay: '3.5s', duration: '5.5s', color: 'star-indigo' },
    { id: 9, top: '80%', left: '25%', delay: '4s', duration: '4s', color: 'star-rose' },
    { id: 10, top: '20%', left: '50%', delay: '0.8s', duration: '5s', color: 'star-teal' },
])
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
</style>
