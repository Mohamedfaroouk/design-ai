<template>
    <div v-motion-fade>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold transition-colors"
                :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ $t('roles.title') }}
            </h1>
            <Button variant="primary" @click="$router.push('/admin/roles/create')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                {{ $t('roles.addNew') }}
            </Button>
        </div>

        <DataTable
            :columns="columns"
            :data="rolesStore.roles"
            :meta="rolesStore.meta"
            :loading="rolesStore.loading"
            @search="handleSearch"
            @sort="handleSort"
            @page-change="handlePageChange"
        >
            <template #cell-name="{ row }">
                <div class="flex items-center gap-2">
                    <span :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-900'">
                        {{ row.name.charAt(0).toUpperCase() + row.name.slice(1) }}
                    </span>
                    <span
                        v-if="row.is_system"
                        class="px-2 py-0.5 text-xs font-medium rounded transition-colors"
                        :class="appStore.darkMode
                            ? 'bg-blue-900/30 text-blue-400 border border-blue-700'
                            : 'bg-blue-100 text-blue-800'"
                    >
                        {{ $t('roles.systemRole') }}
                    </span>
                </div>
            </template>

            <template #cell-permissions="{ row }">
                <span :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-600'">
                    {{ row.permissions?.length || 0 }} {{ $t('roles.fields.permissions').toLowerCase() }}
                </span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <button
                        @click="$router.push(`/admin/roles/${row.id}/edit`)"
                        class="transition-colors"
                        :class="[
                            appStore.darkMode
                                ? 'text-indigo-400 hover:text-indigo-300'
                                : 'text-primary-600 hover:text-primary-900',
                            row.is_system && 'opacity-50 cursor-not-allowed'
                        ]"
                        :disabled="row.is_system"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                    </button>
                    <button
                        @click="confirmDelete(row)"
                        class="transition-colors"
                        :class="[
                            appStore.darkMode
                                ? 'text-red-400 hover:text-red-300'
                                : 'text-red-600 hover:text-red-900',
                            row.is_system && 'opacity-50 cursor-not-allowed'
                        ]"
                        :disabled="row.is_system"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Delete Confirmation Modal -->
        <Modal v-model="showDeleteModal" :title="$t('roles.deleteConfirm')" size="sm">
            <p :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                {{ $t('roles.deleteMessage') }}
            </p>
            <p class="mt-2 font-semibold" :class="appStore.darkMode ? 'text-gray-200' : 'text-gray-900'">
                {{ roleToDelete?.name }}
            </p>

            <template #footer>
                <Button variant="secondary" @click="showDeleteModal = false">
                    {{ $t('common.cancel') }}
                </Button>
                <Button variant="danger" :loading="rolesStore.loading" @click="handleDelete">
                    {{ $t('common.delete') }}
                </Button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAdminRolesStore } from '@/store/admin/roles'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'

const rolesStore = useAdminRolesStore()
const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const filters = ref({
    search: '',
    sortBy: 'created_at',
    sortOrder: 'desc',
    page: 1,
    perPage: 15
})

const columns = computed(() => [
    { key: 'name', label: t('roles.fields.name'), sortable: true },
    { key: 'permissions', label: t('roles.fields.permissions'), sortable: false },
    { key: 'created_at', label: t('roles.fields.createdAt'), sortable: true }
])

const loadRoles = async () => {
    try {
        await rolesStore.fetchList(filters.value)
    } catch (error) {
        toast.error(error.message || t('common.error'))
    }
}

const handleSearch = (query) => {
    filters.value.search = query
    filters.value.page = 1
    loadRoles()
}

const handleSort = ({ column, order }) => {
    filters.value.sortBy = column
    filters.value.sortOrder = order
    loadRoles()
}

const handlePageChange = (page) => {
    filters.value.page = page
    loadRoles()
}

const confirmDelete = (role) => {
    if (role.is_system) {
        toast.warning(t('roles.systemRoleInfo'))
        return
    }
    roleToDelete.value = role
    showDeleteModal.value = true
}

const handleDelete = async () => {
    try {
        const response = await rolesStore.delete(roleToDelete.value.id)
        toast.success(response.message || t('roles.deleteSuccess'))
        showDeleteModal.value = false
        roleToDelete.value = null
        await loadRoles()
    } catch (error) {
        toast.error(error.message || t('common.error'))
    }
}

onMounted(() => {
    loadRoles()
})
</script>
