import { defineStore } from 'pinia'
import rolesService from '@/services/admin/roles'

export const useAdminRolesStore = defineStore('adminRoles', {
  state: () => ({
    roles: [],
    permissions: [],
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.fetchList(params)
        this.roles = response.data
        this.meta = response.meta
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchOne(id) {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.fetchOne(id)
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchPermissions() {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.fetchPermissions()
        this.permissions = response.data
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(data) {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.create(data)
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(id, data) {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.update(id, data)
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async delete(id) {
      this.loading = true
      this.error = null
      try {
        const response = await rolesService.delete(id)
        // Remove from local state
        this.roles = this.roles.filter(role => role.id !== id)
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
