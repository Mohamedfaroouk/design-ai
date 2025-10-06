<template>
    <div class="mb-5">
        <div class="flex items-center justify-between mb-2">
            <label v-if="label" :for="id" class="block text-sm font-semibold"
                   :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
                {{ label }}
                <span v-if="required" class="text-red-500">*</span>
            </label>
            <!-- Character Counter -->
            <span v-if="maxLength" class="text-xs"
                  :class="characterCount > maxLength
                      ? 'text-red-500 font-semibold'
                      : appStore.darkMode ? 'text-gray-500' : 'text-gray-400'">
                {{ characterCount }}/{{ maxLength }}
            </span>
        </div>
        <div class="relative group">
            <textarea
                :id="id"
                :value="modelValue"
                @input="handleInput"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :rows="rows"
                :maxlength="maxLength"
                class="w-full px-4 py-3 border-2 rounded-xl shadow-sm transition-all duration-200 focus:outline-none disabled:cursor-not-allowed backdrop-blur-sm resize-y"
                :class="error
                    ? 'border-red-300 bg-red-50/50 dark:bg-red-900/20 dark:border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-900/50 dark:text-gray-200'
                    : appStore.darkMode
                        ? 'border-gray-600 bg-gray-800 text-gray-200 placeholder-gray-500 hover:border-gray-500 focus:border-primary-400 focus:ring-4 focus:ring-primary-900/50 disabled:bg-gray-900 disabled:text-gray-500'
                        : 'border-primary-200 bg-white/50 placeholder-gray-400 hover:border-primary-300 focus:border-primary focus:ring-4 focus:ring-primary-100 disabled:bg-gray-100 disabled:text-gray-500'"
            ></textarea>

            <!-- Resize Indicator -->
            <div class="absolute end-2 bottom-2 pointer-events-none opacity-40 group-hover:opacity-70 transition-opacity"
                 :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22 22L2 22L22 2L22 22Z" opacity="0.3"/>
                    <path d="M18 22L2 22L18 6L18 22Z" opacity="0.5"/>
                    <path d="M14 22L2 22L14 10L14 22Z"/>
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-2 text-sm flex items-center gap-1"
           :class="appStore.darkMode ? 'text-red-400' : 'text-red-600'">
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
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 4
    },
    maxLength: {
        type: Number,
        default: null
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
    },
    autoGrow: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const id = computed(() => `textarea-${Math.random().toString(36).substr(2, 9)}`)

const characterCount = computed(() => {
    return props.modelValue ? props.modelValue.length : 0
})

const handleInput = (event) => {
    emit('update:modelValue', event.target.value)

    // Auto-grow functionality
    if (props.autoGrow) {
        event.target.style.height = 'auto'
        event.target.style.height = event.target.scrollHeight + 'px'
    }
}
</script>

<style scoped>
/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
    width: 8px;
}

textarea::-webkit-scrollbar-track {
    background: transparent;
}

textarea::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Dark mode scrollbar */
.dark textarea::-webkit-scrollbar-thumb,
textarea.bg-gray-800::-webkit-scrollbar-thumb {
    background: #4b5563;
}

textarea.bg-gray-800::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Smooth resize */
textarea {
    transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
}
</style>
