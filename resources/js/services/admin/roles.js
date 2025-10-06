import api from '../api'

export default {
  /**
   * Fetch all roles with filters
   */
  fetchList(params = {}) {
    return api.get('/admin/roles', params)
  },

  /**
   * Fetch a single role by ID
   */
  fetchOne(id) {
    return api.get(`/admin/roles/${id}`)
  },

  /**
   * Fetch all available permissions
   */
  fetchPermissions() {
    return api.get('/admin/roles/permissions')
  },

  /**
   * Create a new role
   */
  create(data) {
    return api.post('/admin/roles', data)
  },

  /**
   * Update an existing role
   */
  update(id, data) {
    return api.put(`/admin/roles/${id}`, data)
  },

  /**
   * Delete a role
   */
  delete(id) {
    return api.delete(`/admin/roles/${id}`)
  }
}
