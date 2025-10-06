<template>
    <AuthLayout :title="$t('auth.reset.title')" :subtitle="$t('auth.reset.subtitle')">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Illustration/Icon -->
            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-colors"
                     :class="appStore.darkMode ? 'bg-primary-900/30' : 'bg-primary-100'">
                    <svg class="w-8 h-8"
                         :class="appStore.darkMode ? 'text-primary-400' : 'text-primary-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>

            <!-- Password Input -->
            <TextInput
                v-model="form.password"
                :label="$t('auth.reset.newPassword')"
                :placeholder="$t('auth.reset.passwordPlaceholder')"
                :error="getError('password')"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="new-password"
            >
                <template #icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </template>
                <template #suffix>
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="p-1 rounded-lg transition-colors"
                        :class="appStore.darkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500'"
                    >
                        <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </template>
            </TextInput>

            <!-- Password Strength Indicator -->
            <div v-if="form.password" class="space-y-2">
                <div class="flex gap-1">
                    <div
                        v-for="i in 4"
                        :key="i"
                        class="h-1.5 flex-1 rounded-full transition-all duration-300"
                        :class="i <= passwordStrength.level
                            ? passwordStrength.color
                            : appStore.darkMode ? 'bg-gray-700' : 'bg-gray-200'"
                    ></div>
                </div>
                <p class="text-xs font-medium transition-colors"
                   :class="passwordStrength.textColor">
                    {{ passwordStrength.text }}
                </p>
            </div>

            <!-- Confirm Password Input -->
            <TextInput
                v-model="form.password_confirmation"
                :label="$t('auth.reset.confirmPassword')"
                :placeholder="$t('auth.reset.confirmPasswordPlaceholder')"
                :error="getError('password_confirmation')"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                autocomplete="new-password"
            >
                <template #icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template #suffix>
                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="p-1 rounded-lg transition-colors"
                        :class="appStore.darkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500'"
                    >
                        <svg v-if="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </template>
            </TextInput>

            <!-- Password Requirements -->
            <div class="rounded-xl p-4 transition-colors"
                 :class="appStore.darkMode ? 'bg-gray-900/50' : 'bg-gray-50'">
                <h4 class="text-sm font-semibold mb-2 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                    {{ $t('auth.reset.requirements.title') }}
                </h4>
                <ul class="space-y-1 text-xs">
                    <li v-for="(requirement, index) in passwordRequirements"
                        :key="index"
                        class="flex items-center gap-2 transition-colors"
                        :class="requirement.met
                            ? appStore.darkMode ? 'text-success-400' : 'text-success-600'
                            : appStore.darkMode ? 'text-gray-500' : 'text-gray-400'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="requirement.met" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            <circle v-else cx="12" cy="12" r="10" stroke-width="2" />
                        </svg>
                        {{ requirement.text }}
                    </li>
                </ul>
            </div>

            <!-- Submit Button -->
            <Button
                type="submit"
                variant="primary"
                :loading="loading"
                :disabled="!isFormValid"
                class="w-full"
            >
                {{ $t('auth.reset.resetButton') }}
            </Button>
        </form>
    </AuthLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useForm } from '@/composables/useForm'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import authService from '@/services/auth'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()

const email = route.query.email || ''

const { form, errors, loading, getError } = useForm({
    password: '',
    password_confirmation: ''
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const passwordRequirements = computed(() => {
    const password = form.password
    return [
        {
            text: t('auth.reset.requirements.minLength'),
            met: password.length >= 8
        },
        {
            text: t('auth.reset.requirements.uppercase'),
            met: /[A-Z]/.test(password)
        },
        {
            text: t('auth.reset.requirements.lowercase'),
            met: /[a-z]/.test(password)
        },
        {
            text: t('auth.reset.requirements.number'),
            met: /\d/.test(password)
        },
        {
            text: t('auth.reset.requirements.special'),
            met: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        }
    ]
})

const passwordStrength = computed(() => {
    const metRequirements = passwordRequirements.value.filter(r => r.met).length
    const password = form.password

    if (!password) {
        return { level: 0, text: '', color: '', textColor: '' }
    }

    if (metRequirements <= 2) {
        return {
            level: 1,
            text: t('auth.reset.strength.weak'),
            color: 'bg-danger-500',
            textColor: appStore.value.darkMode ? 'text-danger-400' : 'text-danger-600'
        }
    } else if (metRequirements === 3) {
        return {
            level: 2,
            text: t('auth.reset.strength.fair'),
            color: 'bg-warning-500',
            textColor: appStore.value.darkMode ? 'text-warning-400' : 'text-warning-600'
        }
    } else if (metRequirements === 4) {
        return {
            level: 3,
            text: t('auth.reset.strength.good'),
            color: 'bg-primary-500',
            textColor: appStore.value.darkMode ? 'text-primary-400' : 'text-primary-600'
        }
    } else {
        return {
            level: 4,
            text: t('auth.reset.strength.strong'),
            color: 'bg-success-500',
            textColor: appStore.value.darkMode ? 'text-success-400' : 'text-success-600'
        }
    }
})

const isFormValid = computed(() => {
    return (
        form.password &&
        form.password_confirmation &&
        form.password === form.password_confirmation &&
        passwordRequirements.value.every(r => r.met)
    )
})

const handleSubmit = async () => {
    if (!isFormValid.value) return

    loading.value = true
    errors.value = {}

    try {
        const response = await authService.resetPassword({
            email: email,
            password: form.password,
            password_confirmation: form.password_confirmation
        })

        toast.success(response.message || t('auth.reset.success'))

        // Redirect to login page after successful password reset
        setTimeout(() => {
            router.push('/login')
        }, 1500)
    } catch (error) {
        if (error.errors) {
            errors.value = error.errors
        }
        if (error.status !== 422) {
            toast.error(error.message || t('auth.reset.error'))
        }
    } finally {
        loading.value = false
    }
}
</script>
