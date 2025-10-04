<template>
    <div v-motion-fade>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <Button variant="primary" @click="$router.push('/users/create')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Add User
            </Button>
        </div>

        <DataTable
            :columns="columns"
            :data="usersStore.items"
            :meta="usersStore.meta"
            :loading="usersStore.loading"
            @search="handleSearch"
            @sort="handleSort"
            @page-change="handlePageChange"
        >
            <template #cell-avatar="{ row }">
                <div
                    class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center"
                >
                    <span class="text-sm font-medium">{{ row.name.charAt(0) }}</span>
                </div>
            </template>

            <template #cell-status="{ row }">
                <span
                    class="px-2 py-1 text-xs font-medium rounded-full"
                    :class="row.status === 'active'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'"
                >
                    {{ row.status }}
                </span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <button
                        @click="$router.push(`/users/${row.id}/edit`)"
                        class="text-primary-600 hover:text-primary-900"
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
                        class="text-red-600 hover:text-red-900"
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
        <Modal v-model="showDeleteModal" title="Confirm Delete" size="sm">
            <p class="text-gray-700">
                Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?
                This action cannot be undone.
            </p>

            <template #footer>
                <Button variant="secondary" @click="showDeleteModal = false">Cancel</Button>
                <Button variant="danger" :loading="usersStore.loading" @click="handleDelete">
                    Delete
                </Button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUsersStore } from '@/store/users'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'

const usersStore = useUsersStore()
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const filters = ref({
    search: '',
    sort_by: 'created_at',
    sort_order: 'desc',
    page: 1
})

const columns = [
    { key: 'avatar', label: '', sortable: false },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Created', sortable: true }
]

const loadUsers = async () => {
    await usersStore.fetchList(filters.value)
}

const handleSearch = (query) => {
    filters.value.search = query
    filters.value.page = 1
    loadUsers()
}

const handleSort = ({ column, order }) => {
    filters.value.sort_by = column
    filters.value.sort_order = order
    loadUsers()
}

const handlePageChange = (page) => {
    filters.value.page = page
    loadUsers()
}

const confirmDelete = (user) => {
    userToDelete.value = user
    showDeleteModal.value = true
}

const handleDelete = async () => {
    try {
        await usersStore.delete(userToDelete.value.id)
        showDeleteModal.value = false
        userToDelete.value = null
        loadUsers()
    } catch (error) {
        // Error handled by store
    }
}

onMounted(() => {
    loadUsers()
})
</script>
