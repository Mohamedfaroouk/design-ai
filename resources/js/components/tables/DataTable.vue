<template>
    <div class="backdrop-blur-sm rounded-xl shadow-md border overflow-hidden transition-colors"
         :class="appStore.darkMode
             ? 'bg-gray-800/90 border-gray-700'
             : 'bg-white/50 border-primary-100'">
        <!-- Search and filters -->
        <div v-if="searchable || filterable" class="p-3 border-b transition-colors"
             :class="appStore.darkMode
                 ? 'bg-gradient-to-r from-gray-800 to-gray-900 border-gray-700'
                 : 'bg-gradient-to-r from-primary-50 to-secondary-50 border-primary-100'">
            <div class="flex flex-col sm:flex-row gap-2">
                <div v-if="searchable" class="flex-1 relative group">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 group-focus-within:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="localSearch"
                        type="text"
                        :placeholder="t('common.search')"
                        class="w-full ps-9 pe-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 transition-all backdrop-blur-sm"
                        :class="appStore.darkMode
                            ? 'bg-gray-900/50 border-gray-600 text-gray-200 placeholder-gray-500 focus:border-primary-400 focus:ring-primary-900/50'
                            : 'bg-white/50 border-primary-200 focus:border-primary-500 focus:ring-primary-100'"
                    />
                </div>
                <div v-if="filterable" class="flex gap-2">
                    <button
                        @click="showFilters = !showFilters"
                        class="px-3 py-2 text-sm border rounded-lg font-medium transition-all flex items-center gap-2 relative"
                        :class="[
                            appStore.darkMode
                                ? 'bg-gray-900/50 border-gray-600 text-gray-200 hover:bg-gray-700 hover:border-gray-500'
                                : 'bg-white/50 border-primary-200 hover:bg-primary-50 hover:border-primary-300',
                            hasActiveFilters ? 'ring-2 ring-primary-500' : ''
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="hidden sm:inline">{{ t('common.filters') }}</span>
                        <span v-if="activeFiltersCount > 0" class="absolute -top-1 -end-1 w-5 h-5 bg-primary-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            {{ activeFiltersCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-all duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showFilters" @click="showFilters = false" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-150"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showFilters"
                            @click.stop
                            class="rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                            :class="appStore.darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'"
                        >
                            <!-- Header -->
                            <div class="px-6 py-4 border-b flex items-center justify-between"
                                 :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                                <h3 class="text-lg font-semibold"
                                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                                    {{ t('common.filters') }}
                                </h3>
                                <button @click="showFilters = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="p-6 overflow-y-auto max-h-[60vh]">
                                <slot name="filters" :filters="localFilters" :updateFilter="updateFilter"></slot>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t flex items-center justify-between gap-4"
                                 :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 rounded-lg font-medium transition-colors"
                                    :class="appStore.darkMode
                                        ? 'text-gray-300 hover:bg-gray-700'
                                        : 'text-gray-700 hover:bg-gray-100'"
                                >
                                    {{ t('common.clearFilters') }}
                                </button>
                                <div class="flex gap-3">
                                    <button
                                        @click="showFilters = false"
                                        class="px-4 py-2 border-2 rounded-lg font-medium transition-colors"
                                        :class="appStore.darkMode
                                            ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
                                            : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    >
                                        {{ t('common.cancel') }}
                                    </button>
                                    <button
                                        @click="applyFilters"
                                        class="px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors"
                                    >
                                        {{ t('common.apply') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y transition-colors"
                   :class="appStore.darkMode ? 'divide-gray-700' : 'divide-primary-100'">
                <thead class="transition-colors"
                       :class="appStore.darkMode
                           ? 'bg-gradient-to-r from-gray-800 to-gray-900'
                           : 'bg-gradient-to-r from-primary-50 to-secondary-50'">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            @click="column.sortable ? handleSort(column.key) : null"
                            class="px-4 py-3 text-xs font-semibold uppercase tracking-wide transition-colors"
                            :class="[
                                appStore.darkMode ? 'text-gray-300' : 'text-gray-900',
                                column.sortable
                                    ? appStore.darkMode
                                        ? 'cursor-pointer hover:bg-gray-700/50'
                                        : 'cursor-pointer hover:bg-primary-100/50'
                                    : '',
                                column.align === 'center' ? 'text-center' : column.align === 'end' ? 'text-end' : 'text-start'
                            ]"
                        >
                            <div class="flex items-center gap-2" :class="column.align === 'center' ? 'justify-center' : column.align === 'end' ? 'justify-end' : ''">
                                {{ column.label }}
                                <span v-if="column.sortable && sortBy === column.key" class="text-primary-600">
                                    <svg
                                        v-if="sortOrder === 'asc'"
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th
                            v-if="$slots.actions"
                            class="px-4 py-2 text-end text-xs font-semibold uppercase tracking-wide"
                            :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-900'"
                        >
                            {{ t('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="backdrop-blur-sm divide-y transition-colors"
                       :class="appStore.darkMode
                           ? 'bg-gray-900/50 divide-gray-700'
                           : 'bg-white/50 divide-primary-50'">
                    <tr v-if="loading">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-3 text-center">
                            <div class="flex justify-center">
                                <svg
                                    class="animate-spin h-8 w-8 text-primary-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </div>
                        </td>
                    </tr>
                    <tr v-else-if="localData.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-6 text-center"
                            :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <svg class="w-12 h-12 mx-auto mb-3"
                                 :class="appStore.darkMode ? 'text-gray-600' : 'text-gray-300'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="font-medium">{{ emptyText }}</p>
                        </td>
                    </tr>
                    <tr v-else v-for="(row, index) in localData" :key="index" class="transition-colors"
                        :class="appStore.darkMode ? 'hover:bg-gray-700/50' : 'hover:bg-primary-50/50'">
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-4 py-4 text-sm"
                            :class="[
                                appStore.darkMode ? 'text-gray-200' : 'text-gray-900',
                                column.align === 'center' ? 'text-center' : column.align === 'end' ? 'text-end' : 'text-start'
                            ]"
                        >
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                {{ row[column.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="px-4 py-2.5 whitespace-nowrap text-end text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <slot name="actions" :row="row"></slot>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="paginated && meta" class="px-4 py-3 border-t transition-colors"
             :class="appStore.darkMode
                 ? 'bg-gradient-to-r from-gray-800 to-gray-900 border-gray-700'
                 : 'bg-gradient-to-r from-primary-50 to-secondary-50 border-primary-100'">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs font-medium"
                     :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                    {{ t('common.showing') }}
                    <span class="font-medium">{{ meta.from || 0 }}</span>
                    {{ t('common.to') }}
                    <span class="font-medium">{{ meta.to || 0 }}</span>
                    {{ t('common.of') }}
                    <span class="font-medium">{{ meta.total || 0 }}</span>
                    {{ t('common.results') }}
                </div>
                <div class="flex gap-1">
                    <button
                        @click="goToPage(meta.current_page - 1)"
                        :disabled="meta.current_page <= 1"
                        class="px-3 py-1.5 border rounded-lg text-xs font-medium hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        :class="appStore.darkMode
                            ? 'border-gray-600 text-gray-300 hover:bg-gray-700 hover:border-gray-500'
                            : 'border-primary-200 text-gray-700 hover:border-primary-300'"
                    >
                        {{ t('common.previous') }}
                    </button>
                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all min-w-[32px]"
                        :class="
                            page === meta.current_page
                                ? 'bg-gradient-to-r from-primary to-secondary text-white border-transparent shadow-md'
                                : appStore.darkMode
                                    ? 'border border-gray-600 text-gray-300 hover:bg-gray-700 hover:border-gray-500'
                                    : 'border border-primary-200 text-gray-700 hover:bg-white hover:border-primary-300'
                        "
                    >
                        {{ page }}
                    </button>
                    <button
                        @click="goToPage(meta.current_page + 1)"
                        :disabled="meta.current_page >= meta.last_page"
                        class="px-3 py-1.5 border rounded-lg text-xs font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        :class="appStore.darkMode
                            ? 'border-gray-600 text-gray-300 hover:bg-gray-700 hover:border-gray-500'
                            : 'border-primary-200 text-gray-700 hover:bg-white hover:border-primary-300'"
                    >
                        {{ t('common.next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAppStore } from '@/store'
import { useI18n } from 'vue-i18n'

const appStore = useAppStore()
const { t } = useI18n()

const props = defineProps({
    columns: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        default: () => []
    },
    meta: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    },
    searchable: {
        type: Boolean,
        default: true
    },
    filterable: {
        type: Boolean,
        default: false
    },
    paginated: {
        type: Boolean,
        default: true
    },
    emptyText: {
        type: String,
        default: 'No data available'
    },
    searchDebounce: {
        type: Number,
        default: 300
    },
    tableHeight: {
        type: String,
        default: '500px'
    }
})

const emit = defineEmits(['search', 'sort', 'page-change', 'filter'])

const localData = ref(props.data)
const localSearch = ref('')
const sortBy = ref(null)
const sortOrder = ref('asc')
const showFilters = ref(false)
const localFilters = ref({})
const searchTimeout = ref(null)

const visiblePages = computed(() => {
    if (!props.meta) return []

    const current = props.meta.current_page
    const last = props.meta.last_page
    const delta = 2
    const range = []
    const rangeWithDots = []

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        range.push(i)
    }

    if (current - delta > 2) {
        rangeWithDots.push(1, '...')
    } else {
        rangeWithDots.push(1)
    }

    rangeWithDots.push(...range)

    if (current + delta < last - 1) {
        rangeWithDots.push('...', last)
    } else if (last > 1) {
        rangeWithDots.push(last)
    }

    return rangeWithDots.filter(p => p !== '...')
})

const handleSort = (key) => {
    if (sortBy.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = key
        sortOrder.value = 'asc'
    }

    emit('sort', { column: key, order: sortOrder.value })
}

const goToPage = (page) => {
    if (page < 1 || page > props.meta.last_page) return
    emit('page-change', page)
}

// Computed properties for filters
const hasActiveFilters = computed(() => {
    return Object.values(localFilters.value).some(val => {
        if (Array.isArray(val)) return val.length > 0
        return val !== null && val !== undefined && val !== ''
    })
})

const activeFiltersCount = computed(() => {
    return Object.values(localFilters.value).filter(val => {
        if (Array.isArray(val)) return val.length > 0
        return val !== null && val !== undefined && val !== ''
    }).length
})

// Filter methods
const updateFilter = (key, value) => {
    localFilters.value[key] = value
}

const applyFilters = () => {
    emit('filter', localFilters.value)
    showFilters.value = false
}

const clearFilters = () => {
    localFilters.value = {}
    emit('filter', {})
    showFilters.value = false
}

watch(
    () => props.data,
    (newData) => {
        localData.value = newData
    }
)

// Watch search with debounce
watch(localSearch, (newValue) => {
    // Clear existing timeout
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }

    // Set new timeout for debounced search
    searchTimeout.value = setTimeout(() => {
        emit('search', newValue)
    }, props.searchDebounce)
})
</script>
