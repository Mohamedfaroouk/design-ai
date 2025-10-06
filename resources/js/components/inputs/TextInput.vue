<template>
    <div class="mb-5">
        <label v-if="label" :for="id" class="block text-sm font-semibold mb-2"
               :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative group">
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                class="w-full px-4 py-3 border-2 rounded-xl shadow-sm transition-all duration-200 focus:outline-none disabled:cursor-not-allowed backdrop-blur-sm"
                :class="error
                    ? 'border-red-300 bg-red-50/50 dark:bg-red-900/20 dark:border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-900/50 dark:text-gray-200'
                    : appStore.darkMode
                        ? 'border-gray-600 bg-gray-800 text-gray-200 placeholder-gray-500 hover:border-gray-500 focus:border-primary-400 focus:ring-4 focus:ring-primary-900/50 disabled:bg-gray-900 disabled:text-gray-500'
                        : 'border-primary-200 bg-white/50 hover:border-primary-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 disabled:bg-gray-100'"
            />
            <!-- Focus gradient border effect -->
            <div v-if="!error && !disabled" class="absolute inset-0 rounded-xl bg-gradient-to-r from-primary to-secondary opacity-0 group-focus-within:opacity-100 -z-10 blur-sm transition-opacity duration-200"></div>
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-2 text-sm"
           :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">{{ hint }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAppStore } from '@/store'

const appStore = useAppStore()

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    hint: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    }
})

defineEmits(['update:modelValue'])

const id = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)
</script>
