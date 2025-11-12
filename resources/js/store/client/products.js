import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

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
        return new Promise((resolve) => {
          router.get('/client/products', params, {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'meta'],
            onSuccess: (page) => {
              this.products = page.props.products || []
              this.meta = page.props.meta || null
              resolve({
                data: this.products,
                meta: this.meta
              })
            },
            onError: (errors) => {
              this.error = errors.message || 'Failed to fetch products'
              throw errors
            },
            onFinish: () => {
              this.loading = false
            }
          })
        })
      } catch (error) {
        this.error = error.message
        this.loading = false
        throw error
      }
    },

    async fetchOne(id) {
      this.loading = true
      this.error = null
      try {
        return new Promise((resolve) => {
          router.get(`/client/products/${id}`, {}, {
            preserveState: true,
            preserveScroll: true,
            only: ['product'],
            onSuccess: (page) => {
              this.currentProduct = page.props.product || null
              resolve({
                data: this.currentProduct
              })
            },
            onError: (errors) => {
              this.error = errors.message || 'Failed to fetch product'
              throw errors
            },
            onFinish: () => {
              this.loading = false
            }
          })
        })
      } catch (error) {
        this.error = error.message
        this.loading = false
        throw error
      }
    },

    async create(data) {
      this.loading = true
      this.error = null
      try {
        return new Promise((resolve, reject) => {
          router.post('/client/products', data, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
              const newProduct = page.props.product || null
              if (newProduct) {
                this.products.unshift(newProduct)
              }
              resolve({
                data: newProduct
              })
            },
            onError: (errors) => {
              this.error = errors.message || 'Failed to create product'
              reject({ errors })
            },
            onFinish: () => {
              this.loading = false
            }
          })
        })
      } catch (error) {
        this.error = error.message
        this.loading = false
        throw error
      }
    },

    async update(id, data) {
      this.loading = true
      this.error = null
      try {
        return new Promise((resolve, reject) => {
          router.put(`/client/products/${id}`, data, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
              const updatedProduct = page.props.product || null
              if (updatedProduct) {
                const index = this.products.findIndex((p) => p.id === id)
                if (index !== -1) {
                  this.products[index] = updatedProduct
                }
                if (this.currentProduct?.id === id) {
                  this.currentProduct = updatedProduct
                }
              }
              resolve({
                data: updatedProduct
              })
            },
            onError: (errors) => {
              this.error = errors.message || 'Failed to update product'
              reject({ errors })
            },
            onFinish: () => {
              this.loading = false
            }
          })
        })
      } catch (error) {
        this.error = error.message
        this.loading = false
        throw error
      }
    },

    async delete(id) {
      this.loading = true
      this.error = null
      try {
        return new Promise((resolve, reject) => {
          router.delete(`/client/products/${id}`, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
              this.products = this.products.filter((p) => p.id !== id)
              if (this.currentProduct?.id === id) {
                this.currentProduct = null
              }
              resolve({ success: true })
            },
            onError: (errors) => {
              this.error = errors.message || 'Failed to delete product'
              reject({ errors })
            },
            onFinish: () => {
              this.loading = false
            }
          })
        })
      } catch (error) {
        this.error = error.message
        this.loading = false
        throw error
      }
    },

    clearCurrentProduct() {
      this.currentProduct = null
    }
  }
})
