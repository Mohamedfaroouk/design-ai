import api from '../api'

export default {
  /**
   * Fetch all users with filters
   */
  fetchList(params = {}) {
    return api.get('/admin/users', params)
  },

  /**
   * Fetch a single user by ID
   */
  fetchOne(id) {
    return api.get(`/admin/users/${id}`)
  },

  /**
   * Create a new user
   */
  create(data) {
    return api.post('/admin/users', data)
  },

  /**
   * Update an existing user
   */
  update(id, data) {
    return api.put(`/admin/users/${id}`, data)
  },

  /**
   * Delete a user
   */
  delete(id) {
    return api.delete(`/admin/users/${id}`)
  }
}
