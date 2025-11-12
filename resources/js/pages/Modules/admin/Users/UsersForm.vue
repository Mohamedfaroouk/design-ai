<template>
    <div v-motion-fade>
            <Head :title="isEditing ? $t('users.editUser') : $t('users.addNew')" />
            <div class="flex items-center gap-4 mb-6">
                <Link href="/admin/users">
                <button
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
            </Link>
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
                        :error="form.errors.name"
                        required
                    />

                    <!-- Email -->
                    <TextInput
                        v-model="form.email"
                        type="email"
                        :label="$t('users.fields.email')"
                        :placeholder="$t('users.placeholders.email')"
                        :error="form.errors.email"
                        required
                    />

                    <!-- Password -->
                    <TextInput
                        v-model="form.password"
                        type="password"
                        :label="$t('users.fields.password')"
                        :placeholder="$t('users.placeholders.password')"
                        :error="form.errors.password"
                        :required="!isEditing"
                    />

                    <!-- Password Confirmation -->
                    <TextInput
                        v-if="!isEditing || form.password"
                        v-model="form.password_confirmation"
                        type="password"
                        :label="$t('users.fields.passwordConfirmation')"
                        :placeholder="$t('users.placeholders.passwordConfirmation')"
                        :error="form.errors.password_confirmation"
                        :required="!isEditing || !!form.password"
                    />

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <Select
                            v-model="form.role"
                            :label="$t('users.fields.role')"
                            :placeholder="$t('users.placeholders.role')"
                            :options="roleOptions"
                            :error="form.errors.role"
                            required
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t transition-colors"
                     :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <Link href="/admin/users">
                        <Button type="button" variant="secondary">
                            {{ $t('common.cancel') }}
                        </Button>
                    </Link>
                    <Button type="submit" variant="primary" :loading="form.processing">
                        {{ isEditing ? $t('common.update') : $t('common.create') }}
                    </Button>
                </div>
            </form>
        </div>
        </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import TextInput from '@/components/inputs/TextInput.vue'
import Select from '@/components/inputs/Select.vue'
import Button from '@/components/ui/Button.vue'

// Define props received from controller
const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    roles: {
        type: Array,
        required: true
    }
})

const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const isEditing = computed(() => !!props.user)

const roleOptions = computed(() =>
    props.roles.map(role => ({
        value: role.name,
        label: role.name.charAt(0).toUpperCase() + role.name.slice(1)
    }))
)

// Initialize form with Inertia useForm
const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    role: props.user?.roles?.[0]?.name || ''
})

const handleSubmit = () => {
    if (isEditing.value) {
        form.put(`/admin/users/${props.user.id}`, {
            onSuccess: () => {
                toast.success(t('users.updateSuccess'))
            }
        })
    } else {
        form.post('/admin/users', {
            onSuccess: () => {
                toast.success(t('users.createSuccess'))
            }
        })
    }
}
</script>
