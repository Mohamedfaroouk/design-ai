<template>
    <div>
        <Head :title="$t('auth.otp.title')" />
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Illustration/Icon -->
            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-colors"
                     :class="appStore.darkMode ? 'bg-primary-900/30' : 'bg-primary-100'">
                    <svg class="w-8 h-8"
                         :class="appStore.darkMode ? 'text-primary-400' : 'text-primary-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                    </svg>
                </div>
            </div>

            <!-- OTP Input -->
            <TextInput
                v-model="form.otp"
                :label="$t('auth.otp.code')"
                :placeholder="$t('auth.otp.codePlaceholder')"
                :error="form.errors.otp"
                type="text"
                required
                maxlength="6"
                autocomplete="one-time-code"
            >
                <template #icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
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
                {{ $t('auth.otp.verify') }}
            </Button>

            <!-- Resend OTP -->
            <div class="text-center">
                <button
                    type="button"
                    @click="resendOtp"
                    :disabled="form.processing"
                    class="text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-primary-400 hover:text-primary-300' : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ $t('auth.otp.resend') }}
                </button>
            </div>

            <!-- Back Link -->
            <div class="text-center">
                <Link
                    href="/forgot-password"
                    class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-600 hover:text-gray-700'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $t('auth.otp.back') }}
                </Link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'

const props = defineProps({
    email: String
})

const { t } = useI18n()
const appStore = useAppStore()

const form = useForm({
    email: props.email,
    otp: '',
    type: 'password_reset'
})

const handleSubmit = () => {
    form.post('/verify-otp')
}

const resendOtp = () => {
    router.post('/resend-otp', {
        email: props.email,
        type: 'password_reset'
    }, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>
