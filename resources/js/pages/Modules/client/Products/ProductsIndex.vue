<template>
  <div>
    <div v-motion-fade class="min-h-screen p-6 transition-colors">
      <Head :title="$t('products.title')" />
      <div class="mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold" :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
              {{ $t('products.title') }}
            </h1>
            <p class="mt-2" :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
              {{ $t('products.subtitle') }}
            </p>
          </div>
          <Link href="/client/products/create">
            <Button variant="primary">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              {{ $t('products.addNew') }}
            </Button>
          </Link>
        </div>

        <!-- Data Table -->
        <DataTable
          :columns="columns"
          :data="items"
          :meta="pagination"
          :loading="false"
          :filters="filters"
          @search="handleSearch"
          @sort="handleSort"
          @page-change="handlePageChange"
        >
          <template #cell-image_url="{ row }">
            <div v-if="row" class="flex items-center">
              <Link :href="`/client/products/${row.id}`">
                <img
                  v-if="row.image_url || row.latest_image"
                  :src="row.latest_image || row.image_url"
                  :alt="row.name"
                  class="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                />
                <div
                  v-else
                  class="w-12 h-12 rounded flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity"
                  :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-200'"
                >
                  <span class="text-xl">📦</span>
                </div>
              </Link>
            </div>
          </template>

          <template #cell-name="{ row }">
            <div v-if="row">
              <div class="font-medium" :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ row.name }}
              </div>
              <div v-if="row.sku" class="text-sm" :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                {{ $t('products.fields.sku') }}: {{ row.sku }}
              </div>
            </div>
          </template>

          <template #cell-platform="{ row }">
            <span
              v-if="row"
              class="px-2 py-1 rounded-full text-xs font-medium"
              :class="platformColors[row.platform] || platformColors.others"
            >
              {{ $t(`products.platforms.${row.platform}`) }}
            </span>
          </template>

          <template #cell-images_count="{ row }">
            <span
              v-if="row"
              class="px-2 py-1 rounded-full text-xs font-medium"
              :class="
                row.images_count > 0
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                  : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
              "
            >
              {{ row.images_count || 0 }}
            </span>
          </template>

          <template #cell-created_at="{ row }">
            <span v-if="row" :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
              {{ formatDate(row.created_at) }}
            </span>
          </template>

          <template #actions="{ row }">
            <div v-if="row" class="flex gap-2">
              <Link :href="`/client/products/${row.id}`">
                <Button variant="secondary" size="sm">
                  {{ $t('common.view') }}
                </Button>
              </Link>
              <Button variant="danger" size="sm" @click="confirmDelete(row)">
                {{ $t('common.delete') }}
              </Button>
            </div>
          </template>
        </DataTable>
      </div>

      <!-- Delete Confirmation Modal -->
      <Modal v-model="showDeleteModal" :title="$t('products.deleteConfirm.title')" size="sm">
        <p :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
          {{ $t('products.deleteConfirm.message', { name: productToDelete?.name }) }}
        </p>

        <template #footer>
          <div class="flex justify-end gap-3">
            <Button variant="secondary" @click="showDeleteModal = false">
              {{ $t('common.cancel') }}
            </Button>
            <Button variant="danger" @click="handleDelete" :loading="deleteForm.processing">
              {{ $t('common.delete') }}
            </Button>
          </div>
        </template>
      </Modal>
    </div>
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
  }
})

const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()

// Delete modal
const showDeleteModal = ref(false)
const productToDelete = ref(null)
const deleteForm = ref({ processing: false })

// Table columns
const columns = computed(() => [
  { key: 'image_url', label: t('products.fields.image'), sortable: false },
  { key: 'name', label: t('products.fields.name'), sortable: true },
  { key: 'platform', label: t('products.fields.platform'), sortable: true },
  { key: 'images_count', label: t('products.fields.aiImagesCount'), sortable: true },
  { key: 'created_at', label: t('products.fields.createdAt'), sortable: true }
])

// Platform badge colors
const platformColors = {
  salla: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
  zid: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
  others: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

// Event handlers
const handleSearch = (query) => {
  router.get('/client/products', {
    ...props.filters,
    search: query,
    page: 1
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const handleSort = ({ column, order }) => {
  router.get('/client/products', {
    ...props.filters,
    sort_by: column,
    sort_order: order
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const handlePageChange = (page) => {
  router.get('/client/products', {
    ...props.filters,
    page: page
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const handleDelete = () => {
  if (!productToDelete.value) return

  deleteForm.value.processing = true
  router.delete(`/client/products/${productToDelete.value.id}`, {
    onSuccess: () => {
      toast.success(t('products.messages.deleted'))
      showDeleteModal.value = false
      productToDelete.value = null
    },
    onError: () => {
      toast.error(t('common.error'))
    },
    onFinish: () => {
      deleteForm.value.processing = false
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
