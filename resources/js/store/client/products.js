import { defineStore } from 'pinia'
import productsService from '@/services/client/products'

export const useClientProductsStore = defineStore('clientProducts', {
  state: () => ({
    products: [],
    currentProduct: null,
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await productsService.fetchList(params)
        this.products = response.data
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
        const response = await productsService.fetchOne(id)
        this.currentProduct = response.data
        return response
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
        const response = await productsService.create(data)
        this.products.unshift(response.data)
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
        const response = await productsService.update(id, data)
        const index = this.products.findIndex((p) => p.id === id)
        if (index !== -1) {
          this.products[index] = response.data
        }
        if (this.currentProduct?.id === id) {
          this.currentProduct = response.data
        }
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
        const response = await productsService.delete(id)
        this.products = this.products.filter((p) => p.id !== id)
        if (this.currentProduct?.id === id) {
          this.currentProduct = null
        }
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    clearCurrentProduct() {
      this.currentProduct = null
    }
  }
})
