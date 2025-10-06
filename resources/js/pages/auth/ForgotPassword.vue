<template>
    <AuthLayout :title="$t('auth.forgot.title')" :subtitle="$t('auth.forgot.subtitle')">
        <!-- Success State -->
        <div v-if="emailSent" class="text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center"
                     :class="appStore.darkMode ? 'bg-success-900/30' : 'bg-success-100'">
                    <svg class="w-10 h-10"
                         :class="appStore.darkMode ? 'text-success-400' : 'text-success-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                    </svg>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-2 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ $t('auth.forgot.emailSent') }}
                </h3>
                <p class="text-sm transition-colors"
                   :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                    {{ $t('auth.forgot.checkEmail', { email: form.email }) }}
                </p>
            </div>

            <div class="pt-4 space-y-3">
                <Button
                    variant="primary"
                    class="w-full"
                    @click="router.push('/login')"
                >
                    {{ $t('auth.forgot.backToLogin') }}
                </Button>

                <button
                    type="button"
                    @click="resendEmail"
                    :disabled="resendCooldown > 0"
                    class="w-full text-sm font-medium transition-colors"
                    :class="resendCooldown > 0
                        ? 'text-gray-400 cursor-not-allowed'
                        : appStore.darkMode
                            ? 'text-primary-400 hover:text-primary-300'
                            : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ resendCooldown > 0
                        ? $t('auth.forgot.resendIn', { seconds: resendCooldown })
                        : $t('auth.forgot.resendEmail') }}
                </button>
            </div>
        </div>

        <!-- Form State -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
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
                :error="getError('email')"
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
                :loading="loading"
                class="w-full"
            >
                {{ $t('auth.forgot.sendResetLink') }}
            </Button>

            <!-- Back to Login Link -->
            <div class="text-center">
                <router-link
                    to="/login"
                    class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-600 hover:text-gray-700'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $t('auth.forgot.backToLogin') }}
                </router-link>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useForm } from '@/composables/useForm'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import authService from '@/services/auth'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()

const { form, errors, loading, getError } = useForm({
    email: ''
})

const emailSent = ref(false)
const resendCooldown = ref(0)
let cooldownInterval = null

const handleSubmit = async () => {
    loading.value = true
    errors.value = {}

    try {
        const response = await authService.forgotPassword(form)

        emailSent.value = true
        startResendCooldown()
        toast.success(response.message || t('auth.forgot.emailSentSuccess'))

        // Redirect to OTP verification page
        setTimeout(() => {
            router.push({ name: 'otp-verification', query: { email: form.email } })
        }, 2000)
    } catch (error) {
        if (error.errors) {
            errors.value = error.errors
        }
        if (error.status !== 422) {
            toast.error(error.message || t('auth.forgot.error'))
        }
    } finally {
        loading.value = false
    }
}

const resendEmail = async () => {
    if (resendCooldown.value > 0) return

    loading.value = true

    try {
        const response = await authService.resendOtp({
            email: form.email,
            type: 'password_reset'
        })

        startResendCooldown()
        toast.success(response.message || t('auth.forgot.emailResent'))
    } catch (error) {
        toast.error(error.message || t('auth.forgot.resendError'))
    } finally {
        loading.value = false
    }
}

const startResendCooldown = () => {
    resendCooldown.value = 60

    cooldownInterval = setInterval(() => {
        resendCooldown.value--
        if (resendCooldown.value <= 0) {
            clearInterval(cooldownInterval)
        }
    }, 1000)
}

onUnmounted(() => {
    if (cooldownInterval) {
        clearInterval(cooldownInterval)
    }
})
</script>
