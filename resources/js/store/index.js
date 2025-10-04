import { defineStore } from 'pinia'

// Toast notification store
export const useToastStore = defineStore('toast', {
    state: () => ({
        toasts: []
    }),

    actions: {
        add(toast) {
            const id = Date.now()
            this.toasts.push({
                id,
                type: toast.type || 'info',
                message: toast.message,
                duration: toast.duration || 3000
            })

            if (toast.duration !== 0) {
                setTimeout(() => {
                    this.remove(id)
                }, toast.duration || 3000)
            }
        },

        remove(id) {
            const index = this.toasts.findIndex(t => t.id === id)
            if (index > -1) {
                this.toasts.splice(index, 1)
            }
        },

        success(message, duration) {
            this.add({ type: 'success', message, duration })
        },

        error(message, duration) {
            this.add({ type: 'error', message, duration })
        },

        warning(message, duration) {
            this.add({ type: 'warning', message, duration })
        },

        info(message, duration) {
            this.add({ type: 'info', message, duration })
        }
    }
})

// App state store
export const useAppStore = defineStore('app', {
    state: () => ({
        sidebarOpen: true,
        direction: 'ltr'
    }),

    actions: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },

        setDirection(dir) {
            this.direction = dir
            document.documentElement.setAttribute('dir', dir)
        }
    }
})
