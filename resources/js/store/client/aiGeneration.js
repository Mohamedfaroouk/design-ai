import { defineStore } from 'pinia'
import aiGenerationService from '@/services/client/aiGeneration'

export const useClientAiGenerationStore = defineStore('clientAiGeneration', {
  state: () => ({
    generations: [],
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await aiGenerationService.fetchList(params)
        this.generations = response.data
        this.meta = response.meta
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async retry(id) {
      this.loading = true
      this.error = null
      try {
        const response = await aiGenerationService.retry(id)
        return response
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async download(id) {
      try {
        const response = await aiGenerationService.download(id)
        return response
      } catch (error) {
        this.error = error.message
        throw error
      }
    }
  }
})
