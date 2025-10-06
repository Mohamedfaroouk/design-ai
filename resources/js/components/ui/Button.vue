<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        @click="$emit('click', $event)"
        class="inline-flex items-center justify-center gap-2 rounded-xl font-semibold focus:outline-none cursor-pointer transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0"
        :class="buttonClasses"
    >
        <svg
            v-if="loading"
            class="animate-spin h-4 w-4"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>
        <slot></slot>
    </button>
</template>

<script setup>
import { computed } from 'vue'
import { useAppStore } from '@/store'

const appStore = useAppStore()

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning', 'ghost'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])

const buttonClasses = computed(() => {
    const variants = {
        primary: 'bg-gradient-to-r from-primary to-secondary text-white border-transparent hover:from-primary-dark hover:to-secondary-dark focus:ring-primary-300 dark:focus:ring-primary-900/50',
        secondary: appStore.darkMode
            ? 'bg-gray-700 text-gray-200 border-gray-600 hover:bg-gray-600 hover:border-gray-500 focus:ring-gray-700'
            : 'bg-white text-gray-700 border-primary-200 hover:bg-primary-50 hover:border-primary-300 focus:ring-primary-200',
        danger: 'bg-gradient-to-r from-red-600 to-pink-600 text-white border-transparent hover:from-red-700 hover:to-pink-700 focus:ring-red-300 dark:focus:ring-red-900/50',
        success: 'bg-gradient-to-r from-green-600 to-emerald-600 text-white border-transparent hover:from-green-700 hover:to-emerald-700 focus:ring-green-300 dark:focus:ring-green-900/50',
        warning: 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white border-transparent hover:from-yellow-600 hover:to-orange-600 focus:ring-yellow-300 dark:focus:ring-yellow-900/50',
        ghost: appStore.darkMode
            ? 'bg-transparent text-gray-300 border-transparent hover:bg-gray-700 focus:ring-gray-700'
            : 'bg-transparent text-gray-700 border-transparent hover:bg-gray-100 focus:ring-gray-200'
    }

    const sizes = {
        sm: 'text-sm px-3 py-2',
        md: 'text-sm px-5 py-2.5',
        lg: 'text-base px-6 py-3'
    }

    return `${variants[props.variant]} ${sizes[props.size]}`
})
</script>
