import api from './api'

const TOKEN_KEY = 'auth_token'
const USER_KEY = 'auth_user'

export default {
    /**
     * Login with email and password
     */
    async login(credentials) {
        const response = await api.post('/auth/login', credentials)

        if (response.data.token) {
            this.setToken(response.data.token)
            this.setUser(response.data.user)
        }

        return response
    },

    /**
     * Logout current user
     */
    async logout() {
        try {
            await api.post('/auth/logout')
        } finally {
            this.clearAuth()
        }
    },

    /**
     * Send OTP for password reset
     */
    async forgotPassword(data) {
        return await api.post('/auth/forgot-password', data)
    },

    /**
     * Verify OTP code
     */
    async verifyOtp(data) {
        return await api.post('/auth/verify-otp', data)
    },

    /**
     * Reset password with new password
     */
    async resetPassword(data) {
        return await api.post('/auth/reset-password', data)
    },

    /**
     * Resend OTP code
     */
    async resendOtp(data) {
        return await api.post('/auth/resend-otp', data)
    },

    /**
     * Get current authenticated user
     */
    async me() {
        const response = await api.get('/auth/me')

        if (response.data) {
            this.setUser(response.data)
        }

        return response
    },

    /**
     * Set authentication token
     */
    setToken(token) {
        localStorage.setItem(TOKEN_KEY, token)
    },

    /**
     * Get authentication token
     */
    getToken() {
        return localStorage.getItem(TOKEN_KEY)
    },

    /**
     * Set user data
     */
    setUser(user) {
        localStorage.setItem(USER_KEY, JSON.stringify(user))
    },

    /**
     * Get user data
     */
    getUser() {
        const user = localStorage.getItem(USER_KEY)
        return user ? JSON.parse(user) : null
    },

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!this.getToken()
    },

    /**
     * Clear all auth data
     */
    clearAuth() {
        localStorage.removeItem(TOKEN_KEY)
        localStorage.removeItem(USER_KEY)
    },

    /**
     * Check if user has specific role
     */
    hasRole(role) {
        const user = this.getUser()
        return user?.roles?.some(r => r.name === role) || false
    },

    /**
     * Check if user has specific permission
     */
    hasPermission(permission) {
        const user = this.getUser()

        // Check direct permissions
        if (user?.permissions?.some(p => p.name === permission)) {
            return true
        }

        // Check role-based permissions
        if (user?.roles) {
            for (const role of user.roles) {
                if (role.permissions?.some(p => p.name === permission)) {
                    return true
                }
            }
        }

        return false
    }
}
