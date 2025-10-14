<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <h1
        :class="
          appStore.darkMode
            ? 'text-gray-100'
            : 'text-gray-900'
        "
        class="text-2xl font-bold transition-colors"
      >
        {{ $t('ai_generation.title') }}
      </h1>
      <p
        :class="
          appStore.darkMode
            ? 'text-gray-400'
            : 'text-gray-600'
        "
        class="mt-2 transition-colors"
      >
        {{ $t('ai_generation.description') }}
      </p>
    </div>

    <!-- Data Table -->
    <DataTable
      :columns="columns"
      :data="generationStore.generations"
      :meta="generationStore.meta"
      :loading="generationStore.loading"
      :searchable="true"
      :filterable="true"
      :search-placeholder="$t('ai_generation.search_placeholder')"
      @search="handleSearch"
      @sort="handleSort"
      @page-change="handlePageChange"
      @filter="handleFilter"
    >
      <!-- Filters -->
      <template #filters="{ filters, updateFilter }">
        <Select
          :model-value="filters.status"
          :label="$t('ai_generation.filters.status')"
          :options="statusOptions"
          @update:model-value="updateFilter('status', $event)"
        />
      </template>

      <!-- Original Image Column -->
      <template #cell-original_image="{ row }">
        <div v-if="row.original_image_url" class="flex justify-center">
          <img
            :src="row.original_image_url"
            :alt="$t('ai_generation.original_image')"
            class="h-12 w-12 rounded-md object-cover"
          />
        </div>
        <span
          v-else
          :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'"
          class="transition-colors"
        >
          {{ $t('ai_generation.no_image') }}
        </span>
      </template>

      <!-- Generated Image Column -->
      <template #cell-generated_image="{ row }" class="flex justify-center">
        <div v-if="row.generated_image_url" class="flex justify-center">
          <a
            :href="row.generated_image_url"
            target="_blank"
            class="block hover:opacity-80 transition-opacity"
          >
            <img
              :src="row.generated_image_url"
              :alt="$t('ai_generation.generated_image')"
              class="h-16 w-16 rounded-md object-cover shadow-sm"
            />
          </a>
        </div>
        <div v-else-if="row.status === 'failed'" class="flex justify-center">
          <span
            :class="appStore.darkMode ? 'text-red-400' : 'text-red-600'"
            class="text-xs transition-colors"
          >
            {{ $t('ai_generation.statuses.failed') }}
          </span>
        </div>
        <div v-else class="flex justify-center">
          <div class="flex flex-col items-center gap-1">
            <!-- <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div> -->
            <span
              :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'"
              class="text-xs transition-colors"
            >
              {{ $t('ai_generation.processing') }}
            </span>
          </div>
        </div>
      </template>

      <!-- Status Column -->
      <template #cell-status="{ row }">
        <div class="flex items-center gap-2">
          <span
            :class="getStatusClass(row.status)"
            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold transition-colors"
          >
            {{ $t(`ai_generation.statuses.${row.status}`) }}
          </span>
          <!-- Tooltip for failed status -->
          <span
            v-if="row.status === 'failed' && row.error_message"
            v-tooltip="row.error_message"
            :class="appStore.darkMode ? 'text-red-400' : 'text-red-600'"
            class="cursor-help transition-colors"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </span>
        </div>
      </template>

      <!-- Actions Column -->
      <template #actions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <!-- Download Button -->
          <button
            v-if="row.can_download"
            v-tooltip="$t('ai_generation.actions.download')"
            :disabled="downloadingId === row.id"
            :class="[
              appStore.darkMode
                ? 'text-green-400 hover:text-green-300'
                : 'text-green-600 hover:text-green-700',
              downloadingId === row.id ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            ]"
            class="transition-colors"
            @click="handleDownload(row)"
          >
            <svg
              v-if="downloadingId !== row.id"
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
              />
            </svg>
            <Spinner v-else class="h-5 w-5" />
          </button>

          <!-- Retry Button -->
          <button
            v-if="row.can_retry"
            v-tooltip="$t('ai_generation.actions.retry')"
            :class="
              appStore.darkMode
                ? 'text-blue-400 hover:text-blue-300'
                : 'text-blue-600 hover:text-blue-700'
            "
            class="transition-colors cursor-pointer"
            @click="showRetryModal(row)"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Retry Confirmation Modal -->
    <Modal
      v-model="retryModalVisible"
      :title="$t('ai_generation.retry_modal.title')"
      size="sm"
    >
      <div
        :class="
          appStore.darkMode
            ? 'text-gray-300'
            : 'text-gray-700'
        "
        class="transition-colors"
      >
        <p class="mb-4">
          {{ $t('ai_generation.retry_modal.message') }}
        </p>
        <p
          :class="
            appStore.darkMode
              ? 'text-yellow-400'
              : 'text-yellow-600'
          "
          class="text-sm font-semibold transition-colors"
        >
          {{ $t('ai_generation.retry_modal.warning') }}
        </p>
      </div>

      <template #footer>
        <button
          @click="retryModalVisible = false"
          class="px-4 py-2 border-2 rounded-lg font-medium transition-colors"
          :class="appStore.darkMode
            ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
            : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
        >
          {{ $t('common.cancel') }}
        </button>
        <button
          @click="handleRetry"
          :disabled="generationStore.loading"
          class="px-4 py-2 rounded-lg font-medium text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :class="generationStore.loading
            ? 'bg-blue-400 cursor-not-allowed'
            : 'bg-blue-600 hover:bg-blue-700'"
        >
          <span v-if="generationStore.loading">{{ $t('common.loading') }}...</span>
          <span v-else>{{ $t('ai_generation.actions.retry') }}</span>
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store/index'
import { useToastStore } from '@/store/index'
import { useClientAiGenerationStore } from '@/store/client/aiGeneration'
import DataTable from '@/components/tables/DataTable.vue'
import Select from '@/components/inputs/Select.vue'
import Modal from '@/components/ui/Modal.vue'
import Spinner from '@/components/ui/Spinner.vue'

const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()
const generationStore = useClientAiGenerationStore()

// Filters state
const filters = ref({
  search: '',
  sortBy: 'created_at',
  sortOrder: 'desc',
  page: 1,
  perPage: 15,
  status: ''
})

// Table columns
const columns = computed(() => [
  {
    key: 'original_image',
    label: t('ai_generation.columns.original_image'),
    sortable: false,
    align: 'center',
  },
  {
    key: 'generated_image',
    label: t('ai_generation.columns.generated_image'),
    sortable: false,
    align: 'center',
  },
  {
    key: 'status',
    label: t('ai_generation.columns.status'),
    sortable: true,
  },
  {
    key: 'created_at',
    label: t('ai_generation.columns.created_at'),
    sortable: true,
  },
])

// Status filter options
const statusOptions = computed(() => [
  { label: t('ai_generation.filters.all_statuses'), value: '' },
  { label: t('ai_generation.statuses.pending'), value: 'pending' },
  { label: t('ai_generation.statuses.processing'), value: 'processing' },
  { label: t('ai_generation.statuses.completed'), value: 'completed' },
  { label: t('ai_generation.statuses.failed'), value: 'failed' },
])

// Load generations
const loadGenerations = async () => {
  try {
    await generationStore.fetchList(filters.value)
  } catch (error) {
    toast.error(error.message || t('common.error'))
  }
}

// Search handler
const handleSearch = (query) => {
  filters.value.search = query
  filters.value.page = 1
  loadGenerations()
}

// Sort handler
const handleSort = ({ column, order }) => {
  filters.value.sortBy = column
  filters.value.sortOrder = order
  loadGenerations()
}

// Page change handler
const handlePageChange = (page) => {
  filters.value.page = page
  loadGenerations()
}

// Filter handler
const handleFilter = (filterData) => {
  filters.value = { ...filters.value, ...filterData, page: 1 }
  loadGenerations()
}

// Status badge colors
const getStatusClass = (status) => {
  const baseClasses = appStore.darkMode
    ? {
        pending: 'bg-gray-700 text-gray-300',
        processing: 'bg-blue-900 text-blue-300',
        completed: 'bg-green-900 text-green-300',
        failed: 'bg-red-900 text-red-300',
      }
    : {
        pending: 'bg-gray-200 text-gray-700',
        processing: 'bg-blue-100 text-blue-700',
        completed: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
      }

  return baseClasses[status] || baseClasses.pending
}

// Download handling
const downloadingId = ref(null)

const handleDownload = async (item) => {
  downloadingId.value = item.id

  try {
    // If we have a direct image URL, download it directly
    if (item.generated_image_url) {
      const link = document.createElement('a')
      link.href = item.generated_image_url
      link.download = `${item.product_name || 'generated-image'}-${item.id}.png`
      link.target = '_blank'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)

      toast.success(t('ai_generation.messages.download_success'))
    } else {
      // Fallback to API download endpoint
      const response = await generationStore.download(item.id)

      // Create blob and download
      const blob = new Blob([response.data])
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `${item.product_name || 'generated-image'}-${item.id}.png`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)

      toast.success(t('ai_generation.messages.download_success'))
    }
  } catch (error) {
    toast.error(error.message || t('ai_generation.errors.download_failed'))
  } finally {
    downloadingId.value = null
  }
}

// Retry handling
const retryModalVisible = ref(false)
const selectedItem = ref(null)

const showRetryModal = (item) => {
  selectedItem.value = item
  retryModalVisible.value = true
}

const handleRetry = async () => {
  if (!selectedItem.value) return

  try {
    await generationStore.retry(selectedItem.value.id)
    toast.success(t('ai_generation.messages.retry_success'))
    retryModalVisible.value = false
    selectedItem.value = null
    await loadGenerations()
  } catch (error) {
    toast.error(error.message || t('ai_generation.errors.retry_failed'))
  }
}

// Initial load
onMounted(() => {
  loadGenerations()
})
</script>
