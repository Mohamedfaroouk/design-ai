<template>
    <div v-motion-fade>
        <div class="flex items-center gap-4 mb-6">
            <button
                @click="$router.push('/users')"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
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
            <h1 class="text-3xl font-bold text-gray-900">
                {{ isEditing ? 'Edit User' : 'Create User' }}
            </h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <TextInput
                        v-model="form.name"
                        label="Name"
                        placeholder="Enter user name"
                        :error="getError('name')"
                        required
                    />

                    <!-- Email -->
                    <TextInput
                        v-model="form.email"
                        type="email"
                        label="Email"
                        placeholder="Enter email address"
                        :error="getError('email')"
                        required
                    />

                    <!-- Password -->
                    <TextInput
                        v-if="!isEditing"
                        v-model="form.password"
                        type="password"
                        label="Password"
                        placeholder="Enter password"
                        :error="getError('password')"
                        required
                    />

                    <!-- Password Confirmation -->
                    <TextInput
                        v-if="!isEditing"
                        v-model="form.password_confirmation"
                        type="password"
                        label="Confirm Password"
                        placeholder="Re-enter password"
                        :error="getError('password_confirmation')"
                        required
                    />

                    <!-- Role -->
                    <Select
                        v-model="form.role"
                        label="Role"
                        :options="roleOptions"
                        :error="getError('role')"
                        required
                    />

                    <!-- Status -->
                    <Select
                        v-model="form.status"
                        label="Status"
                        :options="statusOptions"
                        :error="getError('status')"
                        required
                    />

                    <!-- Phone -->
                    <TextInput
                        v-model="form.phone"
                        label="Phone"
                        placeholder="Enter phone number"
                        :error="getError('phone')"
                    />

                    <!-- Date of Birth -->
                    <DatePicker
                        v-model="form.date_of_birth"
                        label="Date of Birth"
                        :error="getError('date_of_birth')"
                    />

                    <!-- Avatar -->
                    <div class="md:col-span-2">
                        <ImagePicker
                            v-model="form.avatar"
                            label="Avatar"
                            :error="getError('avatar')"
                            upload-url="/api/upload/avatar"
                            :auto-upload="false"
                        />
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <Textarea
                            v-model="form.bio"
                            label="Bio"
                            placeholder="Enter user bio"
                            :rows="4"
                            :error="getError('bio')"
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
                    <Button type="button" variant="secondary" @click="$router.push('/users')">
                        Cancel
                    </Button>
                    <Button type="submit" variant="primary" :loading="loading">
                        {{ isEditing ? 'Update User' : 'Create User' }}
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
import { useUsersStore } from '@/store/users'
import TextInput from '@/components/inputs/TextInput.vue'
import Textarea from '@/components/inputs/Textarea.vue'
import Select from '@/components/inputs/Select.vue'
import DatePicker from '@/components/inputs/DatePicker.vue'
import ImagePicker from '@/components/inputs/ImagePicker.vue'
import Button from '@/components/ui/Button.vue'

const route = useRoute()
const router = useRouter()
const usersStore = useUsersStore()

const isEditing = computed(() => !!route.params.id)

const { form, errors, loading, getError, post, put } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    status: 'active',
    phone: '',
    date_of_birth: '',
    avatar: '',
    bio: ''
})

const roleOptions = [
    { value: 'admin', label: 'Admin' },
    { value: 'user', label: 'User' },
    { value: 'moderator', label: 'Moderator' }
]

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' }
]

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await put(`/users/${route.params.id}`, {
                successMessage: 'User updated successfully',
                onSuccess: () => router.push('/users')
            })
        } else {
            await post('/users', {
                successMessage: 'User created successfully',
                onSuccess: () => router.push('/users')
            })
        }
    } catch (error) {
        // Error handled by useForm
    }
}

const loadUser = async () => {
    if (isEditing.value) {
        try {
            const response = await usersStore.fetchOne(route.params.id)
            Object.keys(form).forEach(key => {
                if (response.data[key] !== undefined) {
                    form[key] = response.data[key]
                }
            })
        } catch (error) {
            // Error handled by store
            router.push('/users')
        }
    }
}

onMounted(() => {
    loadUser()
})
</script>
