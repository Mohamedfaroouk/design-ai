<template>
    <div v-motion-fade>
        <div class="flex items-center gap-4 mb-6">
            <button
                @click="$router.push('/admin/roles')"
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
                {{ isEditing ? $t('roles.editRole') : $t('roles.addNew') }}
            </h1>
        </div>

        <div v-if="isSystemRole"
             class="mb-4 p-4 rounded-lg transition-colors"
             :class="appStore.darkMode
                 ? 'bg-blue-900/20 border border-blue-700 text-blue-400'
                 : 'bg-blue-50 border border-blue-200 text-blue-800'">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ $t('roles.systemRoleInfo') }}</span>
            </div>
        </div>

        <div class="rounded-lg shadow p-6 transition-colors"
             :class="appStore.darkMode
                 ? 'bg-gray-800 border border-gray-700'
                 : 'bg-white'">
            <form @submit.prevent="handleSubmit">
                <!-- Role Name -->
                <div class="mb-6">
                    <TextInput
                        v-model="form.name"
                        :label="$t('roles.fields.name')"
                        :placeholder="$t('roles.placeholders.name')"
                        :error="getError('name')"
                        :disabled="isSystemRole"
                        required
                    />
                </div>

                <!-- Permissions -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-3 transition-colors"
                           :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-700'">
                        {{ $t('roles.fields.permissions') }}
                    </label>

                    <div v-if="loadingPermissions" class="text-center py-8">
                        <Spinner :text="$t('common.loading')" />
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="(permissions, group) in groupedPermissions" :key="group"
                             class="p-4 rounded-lg transition-colors"
                             :class="appStore.darkMode
                                 ? 'bg-gray-900/50 border border-gray-700'
                                 : 'bg-gray-50 border border-gray-200'">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold transition-colors"
                                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                                    {{ getGroupLabel(group) }}
                                </h3>
                                <button
                                    type="button"
                                    @click="toggleGroup(group, permissions)"
                                    class="text-sm transition-colors"
                                    :class="appStore.darkMode
                                        ? 'text-indigo-400 hover:text-indigo-300'
                                        : 'text-primary-600 hover:text-primary-700'"
                                    :disabled="isSystemRole"
                                >
                                    {{ isGroupSelected(group, permissions) ? $t('common.unselectAll') : $t('common.selectAll') }}
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label
                                    v-for="permission in permissions"
                                    :key="permission.name"
                                    class="flex items-center gap-2 p-2 rounded cursor-pointer transition-colors"
                                    :class="[
                                        appStore.darkMode
                                            ? 'hover:bg-gray-800'
                                            : 'hover:bg-gray-100',
                                        isSystemRole && 'opacity-50 cursor-not-allowed'
                                    ]"
                                >
                                    <input
                                        type="checkbox"
                                        :value="permission.name"
                                        v-model="form.permissions"
                                        :disabled="isSystemRole"
                                        class="w-4 h-4 rounded transition-colors"
                                        :class="appStore.darkMode
                                            ? 'bg-gray-700 border-gray-600 text-indigo-500'
                                            : 'bg-white border-gray-300 text-primary-600'"
                                    />
                                    <span class="text-sm transition-colors"
                                          :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                                        {{ formatPermissionName(permission.name) }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <p v-if="getError('permissions')" class="mt-1 text-sm text-red-500">
                        {{ getError('permissions') }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t transition-colors"
                     :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <Button type="button" variant="secondary" @click="$router.push('/admin/roles')">
                        {{ $t('common.cancel') }}
                    </Button>
                    <Button
                        v-if="!isSystemRole"
                        type="submit"
                        variant="primary"
                        :loading="loading"
                    >
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
import { useAdminRolesStore } from '@/store/admin/roles'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'
import Spinner from '@/components/ui/Spinner.vue'

const route = useRoute()
const router = useRouter()
const rolesStore = useAdminRolesStore()
const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const isEditing = computed(() => !!route.params.id)
const loadingPermissions = ref(false)
const isSystemRole = ref(false)

const { form, errors, loading, getError } = useForm({
    name: '',
    permissions: []
})

const groupedPermissions = computed(() => {
    const groups = {}

    rolesStore.permissions.forEach(permission => {
        const group = permission.name.split('.')[0]
        if (!groups[group]) {
            groups[group] = []
        }
        groups[group].push(permission)
    })

    return groups
})

const getGroupLabel = (group) => {
    const labels = {
        users: t('roles.permissionsGroups.users'),
        roles: t('roles.permissionsGroups.roles'),
        settings: t('roles.permissionsGroups.settings')
    }
    return labels[group] || group.charAt(0).toUpperCase() + group.slice(1)
}

const formatPermissionName = (name) => {
    const parts = name.split('.')
    if (parts.length > 1) {
        return parts[1].charAt(0).toUpperCase() + parts[1].slice(1)
    }
    return name
}

const isGroupSelected = (group, permissions) => {
    return permissions.every(p => form.permissions.includes(p.name))
}

const toggleGroup = (group, permissions) => {
    if (isSystemRole.value) return

    const allSelected = isGroupSelected(group, permissions)
    if (allSelected) {
        form.permissions = form.permissions.filter(
            p => !permissions.some(perm => perm.name === p)
        )
    } else {
        const groupPermissions = permissions.map(p => p.name)
        form.permissions = [...new Set([...form.permissions, ...groupPermissions])]
    }
}

const loadPermissions = async () => {
    loadingPermissions.value = true
    try {
        await rolesStore.fetchPermissions()
    } catch (error) {
        toast.error(error.message || t('common.error'))
    } finally {
        loadingPermissions.value = false
    }
}

const handleSubmit = async () => {
    try {
        const payload = {
            name: form.name,
            permissions: form.permissions
        }

        if (isEditing.value) {
            const response = await rolesStore.update(route.params.id, payload)
            toast.success(response.message || t('roles.updateSuccess'))
            router.push('/admin/roles')
        } else {
            const response = await rolesStore.create(payload)
            toast.success(response.message || t('roles.createSuccess'))
            router.push('/admin/roles')
        }
    } catch (error) {
        if (error.errors) {
            Object.keys(error.errors).forEach(key => {
                errors.value[key] = Array.isArray(error.errors[key])
                    ? error.errors[key][0]
                    : error.errors[key]
            })
        }
        toast.error(error.message || t('common.error'))
    }
}

const loadRole = async () => {
    if (isEditing.value) {
        try {
            const role = await rolesStore.fetchOne(route.params.id)
            form.name = role.name
            form.permissions = role.permissions?.map(p => p.name) || []
            isSystemRole.value = role.is_system || false
        } catch (error) {
            toast.error(error.message || t('common.error'))
            router.push('/admin/roles')
        }
    }
}

onMounted(async () => {
    await loadPermissions()
    await loadRole()
})
</script>
