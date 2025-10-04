import { defineStore } from 'pinia'
import usersService from '@/services/users'
import { useToastStore } from './index'

export const useUsersStore = defineStore('users', {
    state: () => ({
        items: [],
        meta: null,
        loading: false,
        currentUser: null
    }),

    actions: {
        async fetchList(params = {}) {
            this.loading = true
            try {
                const response = await usersService.getUsers(params)
                this.items = response.data || []
                this.meta = response.meta || null
                return response
            } catch (error) {
                const toast = useToastStore()
                toast.error(error.message || 'Failed to fetch users')
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchOne(id) {
            this.loading = true
            try {
                const response = await usersService.getUser(id)
                this.currentUser = response.data
                return response
            } catch (error) {
                const toast = useToastStore()
                toast.error(error.message || 'Failed to fetch user')
                throw error
            } finally {
                this.loading = false
            }
        },

        async create(data) {
            this.loading = true
            try {
                const response = await usersService.createUser(data)
                const toast = useToastStore()
                toast.success('User created successfully')
                return response
            } catch (error) {
                const toast = useToastStore()
                toast.error(error.message || 'Failed to create user')
                throw error
            } finally {
                this.loading = false
            }
        },

        async update(id, data) {
            this.loading = true
            try {
                const response = await usersService.updateUser(id, data)
                const toast = useToastStore()
                toast.success('User updated successfully')
                return response
            } catch (error) {
                const toast = useToastStore()
                toast.error(error.message || 'Failed to update user')
                throw error
            } finally {
                this.loading = false
            }
        },

        async delete(id) {
            this.loading = true
            try {
                await usersService.deleteUser(id)
                this.items = this.items.filter(item => item.id !== id)
                const toast = useToastStore()
                toast.success('User deleted successfully')
            } catch (error) {
                const toast = useToastStore()
                toast.error(error.message || 'Failed to delete user')
                throw error
            } finally {
                this.loading = false
            }
        }
    }
})
