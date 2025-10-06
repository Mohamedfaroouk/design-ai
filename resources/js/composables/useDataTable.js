import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'

/**
 * DataTable Composable
 *
 * Provides reusable DataTable functionality for all index pages
 * Works seamlessly with Laravel HasDataTable trait
 *
 * @param {Function} fetchFunction - API function to fetch data
 * @param {Object} options - Configuration options
 * @returns {Object} DataTable state and methods
 *
 * @example
 * ```js
 * import { useDataTable } from '@/composables/useDataTable'
 * import productsService from '@/services/admin/products'
 *
 * const {
 *   items,
 *   meta,
 *   loading,
 *   handleSearch,
 *   handleSort,
 *   handlePageChange,
 *   handleFilter,
 *   refresh
 * } = useDataTable(productsService.fetchList, {
 *   perPage: 20,
 *   sortBy: 'name',
 *   sortOrder: 'asc'
 * })
 * ```
 */
export function useDataTable(fetchFunction, options = {}) {
    const router = useRouter()
    const route = useRoute()

    // Default options
    const defaultOptions = {
        perPage: 15,
        sortBy: 'created_at',
        sortOrder: 'desc',
        syncWithUrl: true, // Sync state with URL query params
        debounceMs: 300,    // Debounce search input
        ...options
    }

    // State
    const items = ref([])
    const meta = ref({
        current_page: 1,
        last_page: 1,
        per_page: defaultOptions.perPage,
        total: 0,
        from: 0,
        to: 0
    })
    const links = ref({
        first: null,
        last: null,
        prev: null,
        next: null
    })
    const loading = ref(false)
    const error = ref(null)

    // Filters state
    const currentPage = ref(parseInt(route.query.page) || 1)
    const searchQuery = ref(route.query.search || '')
    const sortBy = ref(route.query.sort_by || defaultOptions.sortBy)
    const sortOrder = ref(route.query.sort_order || defaultOptions.sortOrder)
    const perPage = ref(parseInt(route.query.per_page) || defaultOptions.perPage)
    const filters = ref({})

    // Debounce timer
    let debounceTimer = null

    /**
     * Build query parameters from current state
     */
    const buildParams = () => {
        const params = {
            page: currentPage.value,
            per_page: perPage.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            ...filters.value
        }

        if (searchQuery.value) {
            params.search = searchQuery.value
        }

        return params
    }

    /**
     * Update URL query params
     */
    const updateUrl = () => {
        if (!defaultOptions.syncWithUrl) return

        const query = {}

        if (currentPage.value !== 1) query.page = currentPage.value
        if (searchQuery.value) query.search = searchQuery.value
        if (sortBy.value !== defaultOptions.sortBy) query.sort_by = sortBy.value
        if (sortOrder.value !== defaultOptions.sortOrder) query.sort_order = sortOrder.value
        if (perPage.value !== defaultOptions.perPage) query.per_page = perPage.value

        // Add filters to query
        Object.keys(filters.value).forEach(key => {
            if (filters.value[key]) {
                query[key] = filters.value[key]
            }
        })

        router.push({ query })
    }

    /**
     * Fetch data from API
     */
    const fetchData = async () => {
        loading.value = true
        error.value = null

        try {
            const params = buildParams()
            const response = await fetchFunction(params)

            items.value = response.data || []
            meta.value = response.meta || meta.value
            links.value = response.links || links.value
        } catch (err) {
            error.value = err.message || 'Failed to fetch data'
            console.error('DataTable fetch error:', err)
        } finally {
            loading.value = false
        }
    }

    /**
     * Handle search input
     */
    const handleSearch = (query) => {
        searchQuery.value = query
        currentPage.value = 1 // Reset to first page

        // Debounce search
        clearTimeout(debounceTimer)
        debounceTimer = setTimeout(() => {
            updateUrl()
            fetchData()
        }, defaultOptions.debounceMs)
    }

    /**
     * Handle sort change
     */
    const handleSort = (column) => {
        if (sortBy.value === column) {
            // Toggle sort order
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
        } else {
            // New column, default to ascending
            sortBy.value = column
            sortOrder.value = 'asc'
        }

        currentPage.value = 1 // Reset to first page
        updateUrl()
        fetchData()
    }

    /**
     * Handle page change
     */
    const handlePageChange = (page) => {
        currentPage.value = page
        updateUrl()
        fetchData()
    }

    /**
     * Handle per page change
     */
    const handlePerPageChange = (value) => {
        perPage.value = value
        currentPage.value = 1 // Reset to first page
        updateUrl()
        fetchData()
    }

    /**
     * Handle filter change
     */
    const handleFilter = (filterKey, filterValue) => {
        if (filterValue === null || filterValue === undefined || filterValue === '') {
            delete filters.value[filterKey]
        } else {
            filters.value[filterKey] = filterValue
        }

        currentPage.value = 1 // Reset to first page
        updateUrl()
        fetchData()
    }

    /**
     * Handle multiple filters at once
     */
    const handleFilters = (newFilters) => {
        filters.value = { ...filters.value, ...newFilters }
        currentPage.value = 1 // Reset to first page
        updateUrl()
        fetchData()
    }

    /**
     * Clear all filters
     */
    const clearFilters = () => {
        filters.value = {}
        searchQuery.value = ''
        currentPage.value = 1
        updateUrl()
        fetchData()
    }

    /**
     * Refresh data (keep current state)
     */
    const refresh = () => {
        fetchData()
    }

    /**
     * Reset to initial state
     */
    const reset = () => {
        currentPage.value = 1
        searchQuery.value = ''
        sortBy.value = defaultOptions.sortBy
        sortOrder.value = defaultOptions.sortOrder
        perPage.value = defaultOptions.perPage
        filters.value = {}
        updateUrl()
        fetchData()
    }

    /**
     * Go to next page
     */
    const nextPage = () => {
        if (currentPage.value < meta.value.last_page) {
            handlePageChange(currentPage.value + 1)
        }
    }

    /**
     * Go to previous page
     */
    const prevPage = () => {
        if (currentPage.value > 1) {
            handlePageChange(currentPage.value - 1)
        }
    }

    /**
     * Computed: Has previous page
     */
    const hasPrevPage = computed(() => currentPage.value > 1)

    /**
     * Computed: Has next page
     */
    const hasNextPage = computed(() => currentPage.value < meta.value.last_page)

    /**
     * Computed: Is first page
     */
    const isFirstPage = computed(() => currentPage.value === 1)

    /**
     * Computed: Is last page
     */
    const isLastPage = computed(() => currentPage.value === meta.value.last_page)

    /**
     * Computed: Total pages
     */
    const totalPages = computed(() => meta.value.last_page)

    /**
     * Computed: Current sort icon for a column
     */
    const getSortIcon = (column) => {
        if (sortBy.value !== column) return null
        return sortOrder.value === 'asc' ? '↑' : '↓'
    }

    // Initial fetch
    fetchData()

    // Watch route query changes (browser back/forward)
    if (defaultOptions.syncWithUrl) {
        watch(
            () => route.query,
            (newQuery) => {
                currentPage.value = parseInt(newQuery.page) || 1
                searchQuery.value = newQuery.search || ''
                sortBy.value = newQuery.sort_by || defaultOptions.sortBy
                sortOrder.value = newQuery.sort_order || defaultOptions.sortOrder
                perPage.value = parseInt(newQuery.per_page) || defaultOptions.perPage

                // Update filters from query
                const queryFilters = { ...newQuery }
                delete queryFilters.page
                delete queryFilters.search
                delete queryFilters.sort_by
                delete queryFilters.sort_order
                delete queryFilters.per_page
                filters.value = queryFilters

                fetchData()
            },
            { deep: true }
        )
    }

    return {
        // State
        items,
        meta,
        links,
        loading,
        error,
        currentPage,
        searchQuery,
        sortBy,
        sortOrder,
        perPage,
        filters,

        // Methods
        handleSearch,
        handleSort,
        handlePageChange,
        handlePerPageChange,
        handleFilter,
        handleFilters,
        clearFilters,
        refresh,
        reset,
        nextPage,
        prevPage,

        // Computed
        hasPrevPage,
        hasNextPage,
        isFirstPage,
        isLastPage,
        totalPages,
        getSortIcon,

        // Direct fetch (for advanced usage)
        fetchData
    }
}
