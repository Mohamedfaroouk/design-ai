import { ref } from 'vue'
import api from '@/services/api'

export function useFetch(url, options = {}) {
    const data = ref(null)
    const error = ref(null)
    const loading = ref(false)

    const execute = async (params = {}) => {
        loading.value = true
        error.value = null

        try {
            const response = await api.get(url, { ...options.params, ...params })
            data.value = response
            return response
        } catch (err) {
            error.value = err
            throw err
        } finally {
            loading.value = false
        }
    }

    // Auto-execute if immediate option is true
    if (options.immediate !== false) {
        execute()
    }

    return {
        data,
        error,
        loading,
        execute,
        refresh: execute
    }
}
