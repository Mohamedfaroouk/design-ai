<template>
    <div>
        <Head :title="$t('auth.login.title')" />
        
        <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Email Input -->
            <TextInput
                v-model="form.email"
                :label="$t('auth.login.email')"
                :placeholder="$t('auth.login.emailPlaceholder')"
                :error="form.errors.email"
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
                :error="form.errors.password"
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

                <Link
                    href="/forgot-password"
                    class="text-sm font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-primary-400 hover:text-primary-300' : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ $t('auth.login.forgotPassword') }}
                </Link>
            </div>

            <!-- Login Button -->
            <Button
                type="submit"
                variant="primary"
                :loading="form.processing"
                class="w-full"
            >
                {{ $t('auth.login.loginButton') }}
            </Button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm transition-colors"
               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                {{ $t('auth.login.noAccount') }}
                <Link
                    href="/register"
                    class="font-medium transition-colors"
                    :class="appStore.darkMode ? 'text-primary-400 hover:text-primary-300' : 'text-primary-600 hover:text-primary-700'"
                >
                    {{ $t('auth.login.signUp') }}
                </Link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'

const { t } = useI18n()
const appStore = useAppStore()

const form = useForm({
    email: '',
    password: '',
    remember: false
})

const showPassword = ref(false)

const handleLogin = () => {
    form.post('/login', {
        preserveScroll: false,
        preserveState: false,
        replace:true,
        onSuccess: (page) => {
            window.location.reload();
            // Redirect is handled by server
        },
        onError: (errors) => {
            // Errors are automatically handled by Inertia
        }
    })
}
</script>
