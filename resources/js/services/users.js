import api from './api'

export default {
    /**
     * Fetch all users with pagination and filters
     */
    async getUsers(params = {}) {
        return await api.get('/users', params)
    },

    /**
     * Fetch a single user by ID
     */
    async getUser(id) {
        return await api.get(`/users/${id}`)
    },

    /**
     * Create a new user
     */
    async createUser(data) {
        return await api.post('/users', data)
    },

    /**
     * Update an existing user
     */
    async updateUser(id, data) {
        return await api.put(`/users/${id}`, data)
    },

    /**
     * Delete a user
     */
    async deleteUser(id) {
        return await api.delete(`/users/${id}`)
    }
}
