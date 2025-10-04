<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        @click="$emit('click', $event)"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 border rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
        primary: 'bg-primary-600 text-white border-primary-600 hover:bg-primary-700 focus:ring-primary-500',
        secondary: 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 focus:ring-gray-500',
        danger: 'bg-red-600 text-white border-red-600 hover:bg-red-700 focus:ring-red-500',
        success: 'bg-green-600 text-white border-green-600 hover:bg-green-700 focus:ring-green-500',
        warning: 'bg-yellow-600 text-white border-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        ghost: 'bg-transparent text-gray-700 border-transparent hover:bg-gray-100 focus:ring-gray-500'
    }

    const sizes = {
        sm: 'text-sm px-3 py-1.5',
        md: 'text-sm px-4 py-2',
        lg: 'text-base px-6 py-3'
    }

    return `${variants[props.variant]} ${sizes[props.size]}`
})
</script>
