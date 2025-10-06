import { defineStore } from 'pinia'
import usersService from '@/services/admin/users'

export const useAdminUsersStore = defineStore('adminUsers', {
  state: () => ({
    users: [],
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await usersService.fetchList(params)
        this.users = response.data
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
        const response = await usersService.fetchOne(id)
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
        const response = await usersService.create(data)
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
        const response = await usersService.update(id, data)
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
        const response = await usersService.delete(id)
        // Remove from local state
        this.users = this.users.filter(user => user.id !== id)
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
