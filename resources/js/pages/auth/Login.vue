<template>
    <AuthLayout :title="$t('auth.login.title')" :subtitle="$t('auth.login.subtitle')">
        <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Email Input -->
            <TextInput
                v-model="form.email"
                :label="$t('auth.login.email')"
                :placeholder="$t('auth.login.emailPlaceholder')"
                :error="getError('email')"
                type="email"
                required
                autocomplete="email"
            >
                <template #icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </template>
            </TextInput>

            <!-- Password Input -->
            <TextInput
                v-model="form.password"
                :label="$t('auth.login.password')"
                :placeholder="$t('auth.login.passwordPlaceholder')"
                :error="getError('password')"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="current-password"
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

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="w-4 h-4 rounded border-2 transition-all duration-200 cursor-pointer"
                        :class="appStore.darkMode
                            ? 'border-gray-600 bg-gray-700 checked:bg-primary-600 checked:border-primary-600'
                            : 'border-gray-300 bg-white checked:bg-primary-600 checked:border-primary-600'"
                    />
                    <span class="ms-2 text-sm transition-colors"
                          :class="appStore.darkMode ? 'text-gray-300 group-hover:text-gray-200' : 'text-gray-700 group-hover:text-gray-900'">
                        {{ $t('auth.login.rememberMe') }}
                    </span>
                </label>

                <router-link
                    to="/forgot-password"
                    class="text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-primary-400 hover:text-primary-300' : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ $t('auth.login.forgotPassword') }}
                </router-link>
            </div>

            <!-- Login Button -->
            <Button
                type="submit"
                variant="primary"
                :loading="loading"
                class="w-full"
            >
                {{ $t('auth.login.loginButton') }}
            </Button>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t"
                         :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-300'"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 transition-colors"
                          :class="appStore.darkMode ? 'bg-gray-800/50 text-gray-400' : 'bg-white/80 text-gray-500'">
                        {{ $t('auth.login.orContinueWith') }}
                    </span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    @click="handleSocialLogin('google')"
                    class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 font-medium transition-all duration-200"
                    :class="appStore.darkMode
                        ? 'border-gray-700 bg-gray-800/50 text-gray-300 hover:bg-gray-700 hover:border-gray-600'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </button>

                <button
                    type="button"
                    @click="handleSocialLogin('github')"
                    class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 font-medium transition-all duration-200"
                    :class="appStore.darkMode
                        ? 'border-gray-700 bg-gray-800/50 text-gray-300 hover:bg-gray-700 hover:border-gray-600'
                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    GitHub
                </button>
            </div>
        </form>

        <!-- Footer Slot -->
        <template #footer>
            <p class="text-sm transition-colors"
               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                {{ $t('auth.login.noAccount') }}
                <router-link
                    to="/register"
                    class="font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-primary-400 hover:text-primary-300' : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ $t('auth.login.signUp') }}
                </router-link>
            </p>
        </template>
    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
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

const { form, errors, loading, getError } = useForm({
    email: '',
    password: '',
    remember: false
})

const showPassword = ref(false)

const handleLogin = async () => {
    loading.value = true
    errors.value = {}

    try {
        const response = await authService.login(form)

        toast.success(response.message || t('auth.login.success'))

        // Redirect to intended page or dashboard
        const redirect = route.query.redirect || '/admin/dashboard'
        router.push(redirect)
    } catch (error) {
        if (error.errors) {
            errors.value = error.errors
        }
        // Non-field errors will show as toast automatically
        if (error.status !== 422) {
            toast.error(error.message || t('auth.login.error'))
        }
    } finally {
        loading.value = false
    }
}

const handleSocialLogin = (provider) => {
    toast.info(t('auth.login.socialLogin', { provider }))
    // TODO: Implement social login
}
</script>
