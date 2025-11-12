<template>
    <div>
            <Head :title="$t('users.title')" />
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold transition-colors"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ $t('users.title') }}
                </h1>
            <Link href="/admin/users/create">
                <Button variant="primary">
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
            </Link>
        </div>

        <DataTable
            :columns="columns"
            :data="items"
            :meta="pagination"
            :loading="false"
            :filterable="true"
            :filters="filters"
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
                    <Link :href="`/admin/users/${row.id}/edit`">
                        <button
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
                    </Link>
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
                <Button variant="danger" :loading="deleteForm.processing" @click="handleDelete">
                    {{ $t('common.delete') }}
                </Button>
            </template>
        </Modal>
        </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import Select from '@/components/inputs/Select.vue'

// Define props received from controller
const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    pagination: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true
    }
})

const appStore = useAppStore()
const toast = useToastStore()
const { t } = useI18n()

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const deleteForm = ref({ processing: false })

const columns = computed(() => [
    { key: 'avatar', label: '', sortable: false },
    { key: 'name', label: t('users.fields.name'), sortable: true },
    { key: 'email', label: t('users.fields.email'), sortable: true },
    { key: 'role', label: t('users.fields.role'), sortable: false },
    { key: 'created_at', label: t('users.fields.createdAt'), sortable: true }
])

const roleOptions = computed(() => [
    { value: '', label: t('common.all') },
    ...props.roles.map(role => ({
        value: role.id,
        label: role.name
    }))
])

const handleSearch = (query) => {
    router.get('/admin/users', {
        ...props.filters,
        search: query,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const handleSort = ({ column, order }) => {
    router.get('/admin/users', {
        ...props.filters,
        sort_by: column,
        sort_order: order
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const handlePageChange = (page) => {
    router.get('/admin/users', {
        ...props.filters,
        page: page
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const handleFilter = (filterData) => {
    router.get('/admin/users', {
        ...props.filters,
        ...filterData,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const confirmDelete = (user) => {
    userToDelete.value = user
    showDeleteModal.value = true
}

const handleDelete = () => {
    if (!userToDelete.value) return

    deleteForm.value.processing = true
    router.delete(`/admin/users/${userToDelete.value.id}`, {
        onSuccess: () => {
            toast.success(t('users.deleteSuccess'))
            showDeleteModal.value = false
            userToDelete.value = null
        },
        onError: () => {
            toast.error(t('common.error'))
        },
        onFinish: () => {
            deleteForm.value.processing = false
        }
    })
}
</script>
