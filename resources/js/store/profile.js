import { defineStore } from 'pinia'
import profileService from '@/services/profile'

export const useProfileStore = defineStore('profile', {
    state: () => ({
        user: null,
        loading: false,
        saving: false,
    }),

    actions: {
        async fetchProfile() {
            this.loading = true
            try {
                const response = await profileService.get()
                this.user = response.data
                return response
            } finally {
                this.loading = false
            }
        },

        async updateProfile(data) {
            this.saving = true
            try {
                const response = await profileService.update(data)
                this.user = response.data
                return response
            } finally {
                this.saving = false
            }
        },

        async changePassword(data) {
            this.saving = true
            try {
                const response = await profileService.changePassword(data)
                return response
            } finally {
                this.saving = false
            }
        },
    },
})
