<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :id="id"
            ref="dateInput"
            type="text"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
            :class="{ 'border-red-500': error }"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import flatpickr from 'flatpickr'

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
        default: 'Select date...'
    },
    mode: {
        type: String,
        default: 'single', // single, multiple, range
        validator: (value) => ['single', 'multiple', 'range'].includes(value)
    },
    enableTime: {
        type: Boolean,
        default: false
    },
    dateFormat: {
        type: String,
        default: 'Y-m-d'
    },
    minDate: {
        type: String,
        default: null
    },
    maxDate: {
        type: String,
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
    }
})

const emit = defineEmits(['update:modelValue'])

const dateInput = ref(null)
const id = computed(() => `datepicker-${Math.random().toString(36).substr(2, 9)}`)
let flatpickrInstance = null

onMounted(() => {
    flatpickrInstance = flatpickr(dateInput.value, {
        mode: props.mode,
        enableTime: props.enableTime,
        dateFormat: props.dateFormat,
        defaultDate: props.modelValue,
        minDate: props.minDate,
        maxDate: props.maxDate,
        onChange: (selectedDates, dateStr) => {
            emit('update:modelValue', dateStr)
        }
    })
})

watch(() => props.modelValue, (newValue) => {
    if (flatpickrInstance && newValue !== flatpickrInstance.input.value) {
        flatpickrInstance.setDate(newValue)
    }
})

watch(() => props.disabled, (newValue) => {
    if (flatpickrInstance) {
        if (newValue) {
            flatpickrInstance.destroy()
        } else {
            flatpickrInstance = flatpickr(dateInput.value, {
                mode: props.mode,
                enableTime: props.enableTime,
                dateFormat: props.dateFormat,
                defaultDate: props.modelValue,
                onChange: (selectedDates, dateStr) => {
                    emit('update:modelValue', dateStr)
                }
            })
        }
    }
})
</script>
