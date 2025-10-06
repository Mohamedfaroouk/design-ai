<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold transition-colors"
                :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ $t('users.title') }}
            </h1>
            <Button variant="primary" @click="$router.push('/admin/users/create')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                {{ $t('users.addNew') }}
            </Button>
        </div>

        <DataTable
            :columns="columns"
            :data="usersStore.users"
            :meta="usersStore.meta"
            :loading="usersStore.loading"
            :filterable="true"
            @search="handleSearch"
            @sort="handleSort"
            @page-change="handlePageChange"
            @filter="handleFilter"
        >
            <template #filters="{ filters: filterData, updateFilter }">
                <div class="space-y-4">
                    <Select
                        :modelValue="filterData.role"
                        @update:modelValue="updateFilter('role', $event)"
                        :label="$t('users.fields.role')"
                        :options="roleOptions"
                    />
                </div>
            </template>
            <template #cell-avatar="{ row }">
                <div
                    class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center"
                >
                    <span class="text-sm font-medium">{{ row.name.charAt(0) }}</span>
                </div>
            </template>

            <template #cell-role="{ row }">
                <span
                    class="px-2 py-1 text-xs font-medium rounded-full transition-colors"
                    :class="appStore.darkMode
                        ? 'bg-indigo-900/30 text-indigo-400 border border-indigo-700'
                        : 'bg-indigo-100 text-indigo-800'"
                >
                    {{ row.roles?.[0]?.name || 'N/A' }}
                </span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <button
                        @click="$router.push(`/admin/users/${row.id}/edit`)"
                        class="transition-colors"
                        :class="appStore.darkMode
                            ? 'text-indigo-400 hover:text-indigo-300'
                            : 'text-primary-600 hover:text-primary-900'"
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
                        :class="appStore.darkMode
                            ? 'text-red-400 hover:text-red-300'
                            : 'text-red-600 hover:text-red-900'"
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
        <Modal v-model="showDeleteModal" :title="$t('users.deleteConfirm')" size="sm">
            <p :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                {{ $t('users.deleteConfirm') }} <strong>{{ userToDelete?.name }}</strong>?
            </p>

            <template #footer>
                <Button variant="secondary" @click="showDeleteModal = false">
                    {{ $t('common.cancel') }}
                </Button>
                <Button variant="danger" :loading="usersStore.loading" @click="handleDelete">
                    {{ $t('common.delete') }}
                </Button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAdminUsersStore } from '@/store/admin/users'
import { useAdminRolesStore } from '@/store/admin/roles'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import Select from '@/components/inputs/Select.vue'

const usersStore = useAdminUsersStore()
const rolesStore = useAdminRolesStore()
const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const filters = ref({
    search: '',
    sortBy: 'created_at',
    sortOrder: 'desc',
    page: 1,
    perPage: 15,
    role: ''
})

const columns = computed(() => [
    { key: 'avatar', label: '', sortable: false },
    { key: 'name', label: t('users.fields.name'), sortable: true },
    { key: 'email', label: t('users.fields.email'), sortable: true },
    { key: 'role', label: t('users.fields.role'), sortable: false },
    { key: 'created_at', label: t('users.fields.createdAt'), sortable: true }
])

const roleOptions = computed(() => [
    { value: '', label: t('common.all') },
    ...rolesStore.roles.map(role => ({
        value: role.id,
        label: role.name
    }))
])

const loadUsers = async () => {
    try {
        await usersStore.fetchList(filters.value)
    } catch (error) {
        toast.error(error.message || t('common.error'))
    }
}

const handleSearch = (query) => {
    filters.value.search = query
    filters.value.page = 1
    loadUsers()
}

const handleSort = ({ column, order }) => {
    filters.value.sortBy = column
    filters.value.sortOrder = order
    loadUsers()
}

const handlePageChange = (page) => {
    filters.value.page = page
    loadUsers()
}

const handleFilter = (filterData) => {
    filters.value = { ...filters.value, ...filterData, page: 1 }
    loadUsers()
}

const confirmDelete = (user) => {
    userToDelete.value = user
    showDeleteModal.value = true
}

const handleDelete = async () => {
    try {
        const response = await usersStore.delete(userToDelete.value.id)
        toast.success(response.message || t('users.deleteSuccess'))
        showDeleteModal.value = false
        userToDelete.value = null
        await loadUsers()
    } catch (error) {
        toast.error(error.message || t('common.error'))
    }
}

onMounted(async () => {
    await rolesStore.fetchList()
    await loadUsers()
})
</script>
