<template>
    <div>
        <Head :title="$t('auth.forgot.title')" />
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Illustration/Icon -->
            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-colors"
                     :class="appStore.darkMode ? 'bg-primary-900/30' : 'bg-primary-100'">
                    <svg class="w-8 h-8"
                         :class="appStore.darkMode ? 'text-primary-400' : 'text-primary-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- Email Input -->
            <TextInput
                v-model="form.email"
                :label="$t('auth.forgot.email')"
                :placeholder="$t('auth.forgot.emailPlaceholder')"
                :error="form.errors.email"
                type="email"
                required
                autocomplete="email"
            >
                <template #icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </template>
            </TextInput>

            <!-- Submit Button -->
            <Button
                type="submit"
                variant="primary"
                :loading="form.processing"
                class="w-full"
            >
                {{ $t('auth.forgot.sendResetLink') }}
            </Button>

            <!-- Back to Login Link -->
            <div class="text-center">
                <Link
                    href="/login"
                    class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-600 hover:text-gray-700'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $t('auth.forgot.backToLogin') }}
                </Link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'

const { t } = useI18n()
const appStore = useAppStore()

const form = useForm({
    email: ''
})

const handleSubmit = () => {
    form.post('/forgot-password')
}
</script>
