<template>
    <AuthLayout :title="$t('auth.otp.title')" :subtitle="$t('auth.otp.subtitle', { email: maskedEmail })">
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

            <!-- OTP Input Boxes -->
            <div>
                <label class="block text-sm font-semibold mb-3 transition-colors"
                       :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                    {{ $t('auth.otp.enterCode') }}
                </label>

                <div class="flex gap-3 justify-center" :dir="appStore.direction">
                    <input
                        v-for="(digit, index) in otpDigits"
                        :key="index"
                        :ref="el => otpInputs[index] = el"
                        v-model="otpDigits[index]"
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        @input="handleInput(index, $event)"
                        @keydown="handleKeyDown(index, $event)"
                        @paste="handlePaste"
                        class="w-12 h-14 text-center text-2xl font-bold rounded-xl border-2 transition-all duration-200 focus:outline-none"
                        :class="error
                            ? 'border-red-500 bg-red-50/50 dark:bg-red-900/20'
                            : appStore.darkMode
                                ? 'border-gray-600 bg-gray-800 text-gray-200 focus:border-primary-400 focus:ring-4 focus:ring-primary-900/50'
                                : 'border-primary-200 bg-white text-gray-900 focus:border-primary focus:ring-4 focus:ring-primary-100'"
                    />
                </div>

                <p v-if="error" class="mt-3 text-sm flex items-center justify-center gap-1"
                   :class="appStore.darkMode ? 'text-red-400' : 'text-red-600'">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ error }}
                </p>
            </div>

            <!-- Verify Button -->
            <Button
                type="submit"
                variant="primary"
                :loading="loading"
                :disabled="!isOtpComplete"
                class="w-full"
            >
                {{ $t('auth.otp.verify') }}
            </Button>

            <!-- Resend Code -->
            <div class="text-center space-y-2">
                <p class="text-sm transition-colors"
                   :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                    {{ $t('auth.otp.didntReceive') }}
                </p>
                <button
                    type="button"
                    @click="resendOtp"
                    :disabled="resendCooldown > 0"
                    class="text-sm font-medium transition-colors inline-flex items-center gap-2"
                    :class="resendCooldown > 0
                        ? 'text-gray-400 cursor-not-allowed'
                        : appStore.darkMode
                            ? 'text-primary-400 hover:text-primary-300'
                            : 'text-primary-600 hover:text-primary-700'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ resendCooldown > 0
                        ? $t('auth.otp.resendIn', { seconds: resendCooldown })
                        : $t('auth.otp.resendCode') }}
                </button>
            </div>

            <!-- Back Link -->
            <div class="text-center pt-2">
                <router-link
                    to="/login"
                    class="inline-flex items-center gap-2 text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-600 hover:text-gray-700'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $t('auth.otp.backToLogin') }}
                </router-link>
            </div>
        </form>
    </AuthLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import authService from '@/services/auth'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()

const otpDigits = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const loading = ref(false)
const error = ref('')
const resendCooldown = ref(60)
let cooldownInterval = null

const email = route.query.email || 'user@example.com'

const maskedEmail = computed(() => {
    const [name, domain] = email.split('@')
    const maskedName = name.charAt(0) + '*'.repeat(name.length - 2) + name.charAt(name.length - 1)
    return `${maskedName}@${domain}`
})

const isOtpComplete = computed(() => {
    return otpDigits.value.every(digit => digit !== '')
})

const otpCode = computed(() => {
    return otpDigits.value.join('')
})

const handleInput = (index, event) => {
    const value = event.target.value

    // Only allow numbers
    if (!/^\d*$/.test(value)) {
        otpDigits.value[index] = ''
        return
    }

    // Move to next input if digit entered
    if (value && index < otpDigits.value.length - 1) {
        otpInputs.value[index + 1]?.focus()
    }

    error.value = ''
}

const handleKeyDown = (index, event) => {
    // Handle backspace
    if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
        otpInputs.value[index - 1]?.focus()
    }

    // Handle arrow keys
    if (event.key === 'ArrowLeft' && index > 0) {
        otpInputs.value[index - 1]?.focus()
    }
    if (event.key === 'ArrowRight' && index < otpDigits.value.length - 1) {
        otpInputs.value[index + 1]?.focus()
    }
}

const handlePaste = (event) => {
    event.preventDefault()
    const pastedData = event.clipboardData.getData('text').trim()

    // Only process if it's a 6-digit number
    if (/^\d{6}$/.test(pastedData)) {
        const digits = pastedData.split('')
        otpDigits.value = digits
        // Focus the last input
        otpInputs.value[5]?.focus()
        error.value = ''
    }
}

const handleSubmit = async () => {
    if (!isOtpComplete.value) return

    loading.value = true
    error.value = ''

    try {
        const response = await authService.verifyOtp({
            email: email,
            otp: otpCode.value,
            type: 'password_reset'
        })

        toast.success(response.message || t('auth.otp.success'))

        // Redirect to reset password page with email
        router.push({ name: 'reset-password', query: { email: email } })
    } catch (err) {
        error.value = err.message || t('auth.otp.error')
        // Clear OTP inputs
        otpDigits.value = ['', '', '', '', '', '']
        otpInputs.value[0]?.focus()
    } finally {
        loading.value = false
    }
}

const resendOtp = async () => {
    if (resendCooldown.value > 0) return

    loading.value = true

    try {
        const response = await authService.resendOtp({
            email: email,
            type: 'password_reset'
        })

        startResendCooldown()
        toast.success(response.message || t('auth.otp.codeSent'))
    } catch (err) {
        toast.error(err.message || t('auth.otp.resendError'))
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

onMounted(() => {
    // Focus first input
    otpInputs.value[0]?.focus()
    // Start cooldown
    startResendCooldown()
})

onUnmounted(() => {
    if (cooldownInterval) {
        clearInterval(cooldownInterval)
    }
})
</script>
