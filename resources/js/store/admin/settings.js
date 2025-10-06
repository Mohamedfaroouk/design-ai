import { defineStore } from 'pinia'
import settingsService from '@/services/admin/settings'

export const useAdminSettingsStore = defineStore('adminSettings', {
    state: () => ({
        settings: {},
        loading: false,
        saving: false,
    }),

    actions: {
        async fetchAll(params = {}) {
            this.loading = true
            try {
                const response = await settingsService.fetchAll(params)
                this.settings = response.data
                return response
            } finally {
                this.loading = false
            }
        },

        async updateBatch(settings) {
            this.saving = true
            try {
                const response = await settingsService.updateBatch(settings)
                await this.fetchAll() // Refresh after update
                return response
            } finally {
                this.saving = false
            }
        },

        getSettingValue(key) {
            for (const group in this.settings) {
                const setting = this.settings[group].find(s => s.key === key)
                if (setting) {
                    return setting.typed_value
                }
            }
            return null
        },
    },
})
