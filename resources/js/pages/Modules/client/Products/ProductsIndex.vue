<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/store/index'
import { useToastStore } from '@/store/index'
import { useClientProductsStore } from '@/store/client/products'
import DataTable from '@/components/tables/DataTable.vue'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'

const { t } = useI18n()
const router = useRouter()
const appStore = useAppStore()
const toast = useToastStore()
const productsStore = useClientProductsStore()

// Filters state
const filters = ref({
  search: '',
  sortBy: 'created_at',
  sortOrder: 'desc',
  page: 1,
  perPage: 15
})

// Delete modal
const showDeleteModal = ref(false)
const productToDelete = ref(null)

// Table columns
const columns = computed(() => [
  { key: 'image_url', label: t('products.fields.image'), sortable: false },
  { key: 'name', label: t('products.fields.name'), sortable: true },
  { key: 'platform', label: t('products.fields.platform'), sortable: true },
  { key: 'images_count', label: t('products.fields.aiImagesCount'), sortable: true },
  { key: 'created_at', label: t('products.fields.createdAt'), sortable: true },
  { key: 'actions', label: t('common.actions'), sortable: false }
])

// Platform badge colors
const platformColors = {
  salla: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
  zid: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
  others: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

// Ensure products is always an array
const products = computed(() => {
  return Array.isArray(productsStore.products) ? productsStore.products : []
})

// Load data
const loadProducts = async () => {
  try {
    await productsStore.fetchList(filters.value)
  } catch (error) {
    toast.error(error.message || t('common.error'))
  }
}

// Event handlers
const handleSearch = (query) => {
  filters.value.search = query
  filters.value.page = 1
  loadProducts()
}

const handleSort = ({ column, order }) => {
  filters.value.sortBy = column
  filters.value.sortOrder = order
  loadProducts()
}

const handlePageChange = (page) => {
  filters.value.page = page
  loadProducts()
}

const viewProduct = (product) => {
  router.push({ name: 'client.products.detail', params: { id: product.id } })
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const deleteProduct = async () => {
  try {
    await productsStore.delete(productToDelete.value.id)
    toast.success(t('products.messages.deleted'))
    showDeleteModal.value = false
    productToDelete.value = null
    loadProducts()
  } catch (error) {
    toast.error(error.message || t('common.error'))
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  loadProducts()
})
</script>

<template>
  <div  class="min-h-screen p-6 transition-colors">
    <div class=" mx-auto">
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
      </div>

      <!-- Data Table -->
      <DataTable
        :columns="columns"
        :data="products"
        :meta="productsStore.meta"
        :loading="productsStore.loading"
        @search="handleSearch"
        @sort="handleSort"
        @page-change="handlePageChange"
      >
        <template #cell-image_url="{ row }">
          <div v-if="row" class="flex items-center">
            <img
              v-if="row.image_url || row.latest_image"
              :src="row.latest_image || row.image_url"
              :alt="row.name"
              class="w-12 h-12 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
              @click="viewProduct(row)"
            />
            <div
              v-else
              class="w-12 h-12 rounded flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity"
              :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-200'"
              @click="viewProduct(row)"
            >
              <span class="text-xl">📦</span>
            </div>
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
            :class="platformColors[row.platform]"
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

        <template #cell-actions="{ row }">
          <div v-if="row" class="flex gap-2">
            <Button variant="secondary" size="sm" @click="viewProduct(row)">
              {{ $t('common.view') }}
            </Button>
            <Button variant="danger" size="sm" @click="confirmDelete(row)">
              {{ $t('common.delete') }}
            </Button>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #header>
        <h3 class="text-lg font-semibold" :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
          {{ $t('products.deleteConfirm.title') }}
        </h3>
      </template>

      <template #body>
        <p :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
          {{ $t('products.deleteConfirm.message', { name: productToDelete?.name }) }}
        </p>
      </template>

      <template #footer>
        <div class="flex justify-end gap-3">
          <Button variant="secondary" @click="showDeleteModal = false">
            {{ $t('common.cancel') }}
          </Button>
          <Button variant="danger" @click="deleteProduct" :loading="productsStore.loading">
            {{ $t('common.delete') }}
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>
