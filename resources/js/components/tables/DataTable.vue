<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Search and filters -->
        <div v-if="searchable || $slots.filters" class="p-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row gap-4">
                <div v-if="searchable" class="flex-1">
                    <input
                        v-model="localSearch"
                        type="text"
                        placeholder="Search..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <div v-if="$slots.filters">
                    <slot name="filters"></slot>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            @click="column.sortable ? handleSort(column.key) : null"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="{ 'cursor-pointer hover:bg-gray-100': column.sortable }"
                        >
                            <div class="flex items-center gap-2">
                                {{ column.label }}
                                <span v-if="column.sortable && sortBy === column.key">
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
                            class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loading">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-center">
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
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-center text-gray-500">
                            {{ emptyText }}
                        </td>
                    </tr>
                    <tr v-else v-for="(row, index) in localData" :key="index" class="hover:bg-gray-50">
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                {{ row[column.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                            <slot name="actions" :row="row"></slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="paginated && meta" class="px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ meta.from || 0 }}</span>
                    to
                    <span class="font-medium">{{ meta.to || 0 }}</span>
                    of
                    <span class="font-medium">{{ meta.total || 0 }}</span>
                    results
                </div>
                <div class="flex gap-2">
                    <button
                        @click="goToPage(meta.current_page - 1)"
                        :disabled="meta.current_page <= 1"
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Previous
                    </button>
                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        class="px-3 py-1 border rounded-md text-sm font-medium"
                        :class="
                            page === meta.current_page
                                ? 'bg-primary-600 text-white border-primary-600'
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                        "
                    >
                        {{ page }}
                    </button>
                    <button
                        @click="goToPage(meta.current_page + 1)"
                        :disabled="meta.current_page >= meta.last_page"
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

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
    paginated: {
        type: Boolean,
        default: true
    },
    emptyText: {
        type: String,
        default: 'No data available'
    }
})

const emit = defineEmits(['search', 'sort', 'page-change'])

const localData = ref(props.data)
const localSearch = ref('')
const sortBy = ref(null)
const sortOrder = ref('asc')

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

watch(
    () => props.data,
    (newData) => {
        localData.value = newData
    }
)

watch(localSearch, (newValue) => {
    emit('search', newValue)
})
</script>
