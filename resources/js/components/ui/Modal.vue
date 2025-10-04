<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="modelValue"
                @click="handleBackdropClick"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
            >
                <Transition
                    enter-active-class="transition-all duration-300"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition-all duration-300"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="modelValue"
                        @click.stop
                        class="bg-white rounded-lg shadow-xl w-full"
                        :class="sizeClass"
                    >
                        <!-- Header -->
                        <div v-if="$slots.header || title" class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <slot name="header">
                                <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                            </slot>
                            <button
                                v-if="closable"
                                @click="close"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="px-6 py-4">
                            <slot></slot>
                        </div>

                        <!-- Footer -->
                        <div v-if="$slots.footer" class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    },
    closable: {
        type: Boolean,
        default: true
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['update:modelValue', 'close'])

const sizeClass = computed(() => {
    const sizes = {
        sm: 'max-w-md',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl',
        full: 'max-w-full mx-4'
    }
    return sizes[props.size]
})

const close = () => {
    emit('update:modelValue', false)
    emit('close')
}

const handleBackdropClick = () => {
    if (props.closeOnBackdrop && props.closable) {
        close()
    }
}

// Prevent body scroll when modal is open
watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden'
    } else {
        document.body.style.overflow = ''
    }
})
</script>
