<template>
    <Teleport to="body">
        <div class="fixed top-4 end-4 z-50 space-y-2 max-w-sm w-full">
            <TransitionGroup
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 translate-x-full"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition-all duration-300"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 translate-x-full"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="flex items-start gap-3 p-4 rounded-lg shadow-lg border"
                    :class="toastClasses(toast.type)"
                >
                    <div class="flex-shrink-0">
                        <svg
                            v-if="toast.type === 'success'"
                            class="w-6 h-6 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'error'"
                            class="w-6 h-6 text-red-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'warning'"
                            class="w-6 h-6 text-yellow-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-6 h-6 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ toast.message }}</p>
                    </div>
                    <button
                        @click="removeToast(toast.id)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useToastStore } from '@/store'

const toastStore = useToastStore()

const toasts = computed(() => toastStore.toasts)

const toastClasses = (type) => {
    const classes = {
        success: 'bg-green-50 border-green-200',
        error: 'bg-red-50 border-red-200',
        warning: 'bg-yellow-50 border-yellow-200',
        info: 'bg-blue-50 border-blue-200'
    }
    return classes[type] || classes.info
}

const removeToast = (id) => {
    toastStore.remove(id)
}
</script>
