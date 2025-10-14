<template>
    <div v-motion-fade>
        <div class="flex items-center gap-4 mb-6">
            <button
                @click="$router.push('/admin/users')"
                class="p-2 rounded-lg transition-colors"
                :class="appStore.darkMode
                    ? 'hover:bg-gray-700 text-gray-300'
                    : 'hover:bg-gray-100 text-gray-700'"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>
            <h1 class="text-3xl font-bold transition-colors"
                :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ isEditing ? $t('users.editUser') : $t('users.addNew') }}
            </h1>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors"
             :class="appStore.darkMode
                 ? 'bg-gray-800 border border-gray-700'
                 : 'bg-white'">
            <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <TextInput
                        v-model="form.name"
                        :label="$t('users.fields.name')"
                        :placeholder="$t('users.placeholders.name')"
                        :error="getError('name')"
                        required
                    />

                    <!-- Email -->
                    <TextInput
                        v-model="form.email"
                        type="email"
                        :label="$t('users.fields.email')"
                        :placeholder="$t('users.placeholders.email')"
                        :error="getError('email')"
                        required
                    />

                    <!-- Password -->
                    <TextInput
                        v-model="form.password"
                        type="password"
                        :label="$t('users.fields.password')"
                        :placeholder="$t('users.placeholders.password')"
                        :error="getError('password')"
                        :required="!isEditing"
                    />

                    <!-- Password Confirmation -->
                    <TextInput
                        v-if="!isEditing || form.password"
                        v-model="form.password_confirmation"
                        type="password"
                        :label="$t('users.fields.passwordConfirmation')"
                        :placeholder="$t('users.placeholders.passwordConfirmation')"
                        :error="getError('password_confirmation')"
                        :required="!isEditing || !!form.password"
                    />

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <Select
                            v-model="form.role"
                            :label="$t('users.fields.role')"
                            :placeholder="$t('users.placeholders.role')"
                            :options="roleOptions"
                            :error="getError('role')"
                            :disabled="loadingRoles"
                            required
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t transition-colors"
                     :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <Button type="button" variant="secondary" @click="$router.push('/admin/users')">
                        {{ $t('common.cancel') }}
                    </Button>
                    <Button type="submit" variant="primary" :loading="loading">
                        {{ isEditing ? $t('common.update') : $t('common.create') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { useAdminUsersStore } from '@/store/admin/users'
import { useAdminRolesStore } from '@/store/admin/roles'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import TextInput from '@/components/inputs/TextInput.vue'
import Select from '@/components/inputs/Select.vue'
import Button from '@/components/ui/Button.vue'

const route = useRoute()
const router = useRouter()
const usersStore = useAdminUsersStore()
const rolesStore = useAdminRolesStore()
const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const isEditing = computed(() => !!route.params.id)
const loadingRoles = ref(false)
const roleOptions = ref([])

const { form, errors, loading, getError, post, put } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: ''
})

const loadRoles = async () => {
    loadingRoles.value = true
    try {
        const response = await rolesStore.fetchList({ perPage: 100 })
        roleOptions.value = rolesStore.roles.map(role => ({
            value: role.name,
            label: role.name.charAt(0).toUpperCase() + role.name.slice(1)
        }))
    } catch (error) {
        toast.error(error.message || t('common.error'))
    } finally {
        loadingRoles.value = false
    }
}

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await put(`/admin/users/${route.params.id}`, {
                successMessage: t('users.updateSuccess'),
                onSuccess: () => router.push('/admin/users')
            })
        } else {
            await post('/admin/users', {
                successMessage: t('users.createSuccess'),
                onSuccess: () => router.push('/admin/users')
            })
        }
    } catch (error) {
        // Errors are already handled by useForm composable
        // Field errors will be shown inline via errors reactive object
    }
}

const loadUser = async () => {
    if (isEditing.value) {
        try {
            const user = await usersStore.fetchOne(route.params.id)
            form.name = user.name
            form.email = user.email
            form.role = user.roles?.[0]?.name || ''
        } catch (error) {
            toast.error(error.message || t('common.error'))
            router.push('/admin/users')
        }
    }
}

onMounted(async () => {
    await loadRoles()
    await loadUser()
})
</script>
