<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold transition-colors"
                :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ $t('profile.title') }}
            </h1>
            <p class="mt-2 text-sm transition-colors"
               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                {{ $t('profile.subtitle') }}
            </p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <Spinner size="lg" :text="$t('common.loading')" />
        </div>

        <!-- Profile Content -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Personal Information Card -->
            <div class="rounded-lg shadow p-6 transition-colors"
                 :class="appStore.darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white'">
                <h2 class="text-xl font-semibold mb-6 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ $t('profile.personalInfo') }}
                </h2>

                <form @submit.prevent="handleUpdateProfile" class="space-y-4">
                    <TextInput
                        v-model="profileForm.name"
                        :label="$t('profile.fields.name')"
                        :placeholder="$t('profile.placeholders.name')"
                        :error="profileErrors.name"
                        required
                    />

                    <TextInput
                        v-model="profileForm.email"
                        type="email"
                        :label="$t('profile.fields.email')"
                        :placeholder="$t('profile.placeholders.email')"
                        :error="profileErrors.email"
                        required
                    />

                    <div class="flex justify-end gap-4 pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="resetProfileForm"
                            :disabled="profileSaving"
                        >
                            {{ $t('common.cancel') }}
                        </Button>
                        <Button
                            type="submit"
                            variant="primary"
                            :loading="profileSaving"
                        >
                            {{ $t('common.update') }}
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="rounded-lg shadow p-6 transition-colors"
                 :class="appStore.darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white'">
                <h2 class="text-xl font-semibold mb-6 transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ $t('profile.changePassword') }}
                </h2>

                <form @submit.prevent="handleChangePassword" class="space-y-4">
                    <TextInput
                        v-model="passwordForm.current_password"
                        type="password"
                        :label="$t('profile.fields.currentPassword')"
                        :placeholder="$t('profile.placeholders.currentPassword')"
                        :error="passwordErrors.current_password"
                        required
                    />

                    <TextInput
                        v-model="passwordForm.password"
                        type="password"
                        :label="$t('profile.fields.newPassword')"
                        :placeholder="$t('profile.placeholders.newPassword')"
                        :error="passwordErrors.password"
                        required
                    />

                    <TextInput
                        v-model="passwordForm.password_confirmation"
                        type="password"
                        :label="$t('profile.fields.confirmPassword')"
                        :placeholder="$t('profile.placeholders.confirmPassword')"
                        :error="passwordErrors.password_confirmation"
                        required
                    />

                    <!-- Password Requirements -->
                    <div class="rounded-lg p-4 transition-colors"
                         :class="appStore.darkMode ? 'bg-gray-900/50 border border-gray-700' : 'bg-gray-50 border border-gray-200'">
                        <p class="text-sm font-medium mb-2 transition-colors"
                           :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                            {{ $t('profile.password.requirements.title') }}
                        </p>
                        <ul class="space-y-1 text-sm transition-colors"
                            :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" :class="hasMinLength ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $t('profile.password.requirements.minLength') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" :class="hasUppercase ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $t('profile.password.requirements.uppercase') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" :class="hasLowercase ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $t('profile.password.requirements.lowercase') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" :class="hasNumber ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $t('profile.password.requirements.number') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" :class="hasSpecial ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $t('profile.password.requirements.special') }}
                            </li>
                        </ul>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="resetPasswordForm"
                            :disabled="passwordSaving"
                        >
                            {{ $t('common.cancel') }}
                        </Button>
                        <Button
                            type="submit"
                            variant="primary"
                            :loading="passwordSaving"
                        >
                            {{ $t('profile.changePassword') }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useProfileStore } from '@/store/profile'
import { useForm } from '@/composables/useForm'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'
import Spinner from '@/components/ui/Spinner.vue'

const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()
const profileStore = useProfileStore()

const loading = ref(false)

// Profile Update Form
const {
    form: profileForm,
    errors: profileErrors,
    loading: profileSaving,
    getError: getProfileError,
    put: updateProfile,
    reset: resetProfileFormData
} = useForm({
    name: '',
    email: ''
})

// Password Change Form
const {
    form: passwordForm,
    errors: passwordErrors,
    loading: passwordSaving,
    getError: getPasswordError,
    put: changePassword,
    reset: resetPasswordFormData
} = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

// Password validation computed properties
const hasMinLength = computed(() => passwordForm.password.length >= 8)
const hasUppercase = computed(() => /[A-Z]/.test(passwordForm.password))
const hasLowercase = computed(() => /[a-z]/.test(passwordForm.password))
const hasNumber = computed(() => /[0-9]/.test(passwordForm.password))
const hasSpecial = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(passwordForm.password))

const loadProfile = async () => {
    loading.value = true
    try {
        await profileStore.fetchProfile()

        // Populate profile form
        profileForm.name = profileStore.user.name
        profileForm.email = profileStore.user.email
    } catch (error) {
        toast.error(error.message || t('profile.updateError'))
    } finally {
        loading.value = false
    }
}

const handleUpdateProfile = async () => {
    try {
        await updateProfile('/profile', {
            successMessage: t('profile.updateSuccess'),
            onSuccess: () => {
                loadProfile()
            }
        })
    } catch (error) {
        // Errors are handled by useForm composable
    }
}

const handleChangePassword = async () => {
    try {
        await changePassword('/profile/change-password', {
            successMessage: t('profile.password.changed'),
            onSuccess: () => {
                resetPasswordForm()
            }
        })
    } catch (error) {
        // Errors are handled by useForm composable
    }
}

const resetProfileForm = () => {
    profileForm.name = profileStore.user.name
    profileForm.email = profileStore.user.email
    resetProfileFormData()
}

const resetPasswordForm = () => {
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
    resetPasswordFormData()
}

onMounted(() => {
    loadProfile()
})
</script>
