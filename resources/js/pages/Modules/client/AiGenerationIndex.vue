<template>
  <div>
    <div>
      <Head :title="$t('ai_generation.title')" />
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
        
      </div>

      <!-- Data Table -->
      <DataTable
        :columns="columns"
        :data="items"
        :meta="pagination"
        :loading="false"
        :searchable="false"
        :filterable="true"
        :filters="filters"
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
            loading="lazy"
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
        <div v-if="row.generated_image_url || hasValidThumbnail(row)" class="flex justify-center">
          <button
            type="button"
            class="block group relative focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md"
            @click="openImageModal(row)"
          >
            <!-- Image Container with skeleton loader -->
            <div class="relative h-16 w-16">
              <!-- Skeleton Loader -->
              <div
                v-if="!imageLoaded[row.id]"
                class="absolute inset-0 rounded-md animate-pulse"
                :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-200'"
              >
                <div class="flex items-center justify-center h-full">
                  <svg
                    class="h-8 w-8"
                    :class="appStore.darkMode ? 'text-gray-600' : 'text-gray-300'"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                </div>
              </div>

              <!-- Actual Image -->
              <img
                :src="getOptimalThumbnail(row)"
                :alt="$t('ai_generation.generated_image')"
                class="h-16 w-16 rounded-md object-cover shadow-sm transition-all duration-300"
                :class="[
                  imageLoaded[row.id] ? 'opacity-100 scale-100' : 'opacity-0 scale-95',
                  'group-hover:scale-105 group-hover:shadow-md'
                ]"
                loading="lazy"
                @load="handleImageLoad(row.id)"
                @error="handleImageError(row.id)"
              />

              <!-- Zoom icon overlay -->
              <div
                v-if="imageLoaded[row.id]"
                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-md"
                :class="appStore.darkMode ? 'bg-gray-900/60' : 'bg-black/30'"
              >
                <svg
                  class="h-6 w-6 text-white drop-shadow-lg"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"
                  />
                </svg>
              </div>

              <!-- Error indicator -->
              <div
                v-if="imageErrors[row.id]"
                class="absolute inset-0 flex items-center justify-center rounded-md"
                :class="appStore.darkMode ? 'bg-gray-800 text-gray-400' : 'bg-gray-100 text-gray-500'"
              >
                <svg
                  class="h-8 w-8"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
          </button>
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
            <!-- <div class="animate-spin rounded-full h-6 w-6 border-2 border-gray-300 border-t-blue-600"></div> -->
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
            <!-- <Spinner v-else class="h-5 w-5" /> -->
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

    <!-- Image Preview Modal -->
    <Modal
      v-model="imageModalVisible"
      :title="$t('ai_generation.preview_modal.title')"
      size="lg"
    >
      <div
        v-if="selectedImage"
        class="flex flex-col items-center gap-4"
      >
        <!-- Image Container with Loading State -->
        <div class="relative w-full flex justify-center">
          <!-- Loading Skeleton -->
          <div
            v-if="!modalImageLoaded"
            class="absolute inset-0 flex items-center justify-center"
          >
            <div
              class="rounded-lg animate-pulse max-w-full max-h-[70vh] aspect-square"
              :class="appStore.darkMode ? 'bg-gray-700' : 'bg-gray-200'"
              style="width: 512px; height: 512px;"
            >
              <div class="flex items-center justify-center h-full">
                <svg
                  class="h-16 w-16"
                  :class="appStore.darkMode ? 'text-gray-600' : 'text-gray-300'"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Actual Image -->
          <img
            :src="getThumbnailUrl(selectedImage, 'large') || getThumbnailUrl(selectedImage, 'original') || selectedImage.generated_image_url"
            :alt="$t('ai_generation.generated_image')"
            class="max-w-full max-h-[70vh] rounded-lg shadow-lg transition-opacity duration-300"
            :class="[
              appStore.darkMode ? 'bg-gray-800' : 'bg-white',
              modalImageLoaded ? 'opacity-100' : 'opacity-0'
            ]"
            @load="modalImageLoaded = true"
            @error="handleModalImageError"
          />
        </div>

        <!-- Image Details -->
        <div
          class="w-full space-y-2 text-sm"
          :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
        >
          <p v-if="selectedImage.product_name">
            <strong>{{ $t('ai_generation.preview_modal.product') }}:</strong> {{ selectedImage.product_name }}
          </p>
          <p v-if="selectedImage.input_data?.prompt">
            <strong>{{ $t('ai_generation.preview_modal.prompt') }}:</strong> {{ selectedImage.input_data.prompt }}
          </p>
          <p>
            <strong>{{ $t('ai_generation.preview_modal.status') }}:</strong>
            <span :class="getStatusClass(selectedImage.status)" class="ms-2 px-2 py-1 rounded-full text-xs">
              {{ $t(`ai_generation.statuses.${selectedImage.status}`) }}
            </span>
          </p>
          <!-- Thumbnail Info (for debugging) -->
          <p v-if="selectedImage.thumbnails" class="text-xs opacity-60">
            <strong>{{ $t('ai_generation.preview_modal.quality') }}:</strong>
            <span v-if="getThumbnailUrl(selectedImage, 'large')">{{ $t('ai_generation.preview_modal.high_quality') }}</span>
            <span v-else-if="getThumbnailUrl(selectedImage, 'preview')">{{ $t('ai_generation.preview_modal.medium_quality') }}</span>
            <span v-else>{{ $t('ai_generation.preview_modal.original_quality') }}</span>
          </p>
        </div>
      </div>

      <template #footer>
        <button
          @click="imageModalVisible = false"
          class="px-4 py-2 border-2 rounded-lg font-medium transition-colors"
          :class="appStore.darkMode
            ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
            : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
        >
          {{ $t('common.close') }}
        </button>
        <button
          v-if="selectedImage?.can_download"
          @click="handleDownload(selectedImage)"
          :disabled="downloadingId === selectedImage?.id"
          class="px-4 py-2 rounded-lg font-medium text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :class="downloadingId === selectedImage?.id
            ? 'bg-green-400 cursor-not-allowed'
            : 'bg-green-600 hover:bg-green-700'"
        >
          <span v-if="downloadingId === selectedImage?.id">{{ $t('common.loading') }}...</span>
          <span v-else>{{ $t('ai_generation.actions.download') }}</span>
        </button>
      </template>
    </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store/index'
import { useToastStore } from '@/store/index'
import DataTable from '@/components/tables/DataTable.vue'
import Select from '@/components/inputs/Select.vue'
import Modal from '@/components/ui/Modal.vue'
import Spinner from '@/components/ui/Spinner.vue'

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

// Image loading states
const imageLoaded = ref({})
const imageErrors = ref({})
const modalImageLoaded = ref(false)

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
    resetImageStates()
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

  // Note: Retry functionality would need to be implemented in the controller
  // For now, we'll just show a message
  toast.info(t('ai_generation.messages.retry_coming_soon'))
  retryModalVisible.value = false
  selectedItem.value = null
}

// Image preview modal
const imageModalVisible = ref(false)
const selectedImage = ref(null)

const openImageModal = (item) => {
  selectedImage.value = item
  imageModalVisible.value = true
  modalImageLoaded.value = false
}

// Image loading handlers
const handleImageLoad = (itemId) => {
  imageLoaded.value[itemId] = true
}

const handleImageError = (itemId) => {
  imageErrors.value[itemId] = true
  console.error(`Failed to load image for item ${itemId}`)
}

const handleModalImageError = () => {
  toast.error(t('ai_generation.errors.image_load_failed'))
  modalImageLoaded.value = true // Show error state
}

// Check if item has valid thumbnail
const hasValidThumbnail = (item) => {
  return !!(
    (item.thumbnails && Object.keys(item.thumbnails).length > 0) ||
    (item.downloaded_images && item.downloaded_images.length > 0)
  )
}

// Get thumbnail URL helper with priority
const getThumbnailUrl = (item, size = 'thumb') => {
  if (!item) return null

  // Priority 1: Spatie Media Library thumbnails
  if (item.thumbnails && item.thumbnails[size]) {
    return item.thumbnails[size]
  }

  // Priority 2: downloaded_images data
  if (item.downloaded_images && item.downloaded_images.length > 0) {
    const sizeMap = {
      thumb: 'thumb_url',
      preview: 'preview_url',
      large: 'large_url',
      original: 'stored_url'
    }
    return item.downloaded_images[0][sizeMap[size]] || null
  }

  return null
}

// Get optimal thumbnail for display (smart selection)
const getOptimalThumbnail = (item) => {
  // Try to get thumbnail first (100x100 for table)
  const thumbUrl = getThumbnailUrl(item, 'thumb')
  if (thumbUrl) return thumbUrl

  // Fallback to preview (300x300)
  const previewUrl = getThumbnailUrl(item, 'preview')
  if (previewUrl) return previewUrl

  // Fallback to stored URL
  const storedUrl = getThumbnailUrl(item, 'original')
  if (storedUrl) return storedUrl

  // Final fallback to generated_image_url
  return item.generated_image_url
}

// Reset loading states when data changes
const resetImageStates = () => {
  imageLoaded.value = {}
  imageErrors.value = {}
}
</script>
