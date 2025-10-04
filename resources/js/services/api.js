import axios from 'axios'

// Create axios instance
const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
})

// Request interceptor
api.interceptors.request.use(
    (config) => {
        // Add auth token if available
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }

        // Add CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken
        }

        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor
api.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        // Normalize error response
        const normalizedError = {
            message: 'An error occurred',
            errors: {},
            status: error.response?.status
        }

        if (error.response) {
            normalizedError.message = error.response.data?.message || error.message
            normalizedError.errors = error.response.data?.errors || {}

            // Handle 401 Unauthorized
            if (error.response.status === 401) {
                localStorage.removeItem('auth_token')
                window.location.href = '/login'
            }

            // Handle 403 Forbidden
            if (error.response.status === 403) {
                normalizedError.message = 'You do not have permission to perform this action'
            }

            // Handle 404 Not Found
            if (error.response.status === 404) {
                normalizedError.message = 'Resource not found'
            }

            // Handle 422 Validation Error
            if (error.response.status === 422) {
                normalizedError.message = 'Validation failed'
            }

            // Handle 500 Server Error
            if (error.response.status === 500) {
                normalizedError.message = 'Server error. Please try again later.'
            }
        } else if (error.request) {
            normalizedError.message = 'No response from server. Please check your connection.'
        }

        return Promise.reject(normalizedError)
    }
)

// API service wrapper
export default {
    /**
     * GET request
     */
    async get(url, params = {}) {
        const response = await api.get(url, { params })
        return response.data
    },

    /**
     * POST request
     */
    async post(url, data = {}) {
        const response = await api.post(url, data)
        return response.data
    },

    /**
     * PUT request
     */
    async put(url, data = {}) {
        const response = await api.put(url, data)
        return response.data
    },

    /**
     * PATCH request
     */
    async patch(url, data = {}) {
        const response = await api.patch(url, data)
        return response.data
    },

    /**
     * DELETE request
     */
    async delete(url) {
        const response = await api.delete(url)
        return response.data
    },

    /**
     * Upload file(s)
     */
    async upload(url, formData, onUploadProgress = null) {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }

        if (onUploadProgress) {
            config.onUploadProgress = onUploadProgress
        }

        const response = await api.post(url, formData, config)
        return response.data
    }
}
