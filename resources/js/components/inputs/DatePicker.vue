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
                ref="dateInput"
                type="text"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                class="w-full px-4 py-3 ps-11 border-2 rounded-xl shadow-sm transition-all duration-200 focus:outline-none disabled:cursor-not-allowed backdrop-blur-sm"
                :class="error
                    ? 'border-red-300 bg-red-50/50 dark:bg-red-900/20 dark:border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-900/50 dark:text-gray-200'
                    : appStore.darkMode
                        ? 'border-gray-600 bg-gray-800 text-gray-200 placeholder-gray-500 hover:border-gray-500 focus:border-primary-400 focus:ring-4 focus:ring-primary-900/50 disabled:bg-gray-900 disabled:text-gray-500'
                        : 'border-primary-200 bg-white/50 placeholder-gray-400 hover:border-primary-300 focus:border-primary focus:ring-4 focus:ring-primary-100 disabled:bg-gray-100 disabled:text-gray-500'"
            />
            <!-- Calendar Icon -->
            <div class="absolute start-3 top-1/2 -translate-y-1/2 pointer-events-none transition-colors"
                 :class="disabled
                     ? appStore.darkMode ? 'text-gray-600' : 'text-gray-400'
                     : appStore.darkMode ? 'text-gray-400 group-hover:text-primary-400' : 'text-gray-500 group-hover:text-primary-600'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
import { ref, computed, onMounted, watch } from 'vue'
import flatpickr from 'flatpickr'
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

const initFlatpickr = () => {
    return flatpickr(dateInput.value, {
        mode: props.mode,
        enableTime: props.enableTime,
        dateFormat: props.dateFormat,
        defaultDate: props.modelValue,
        minDate: props.minDate,
        maxDate: props.maxDate,
        onChange: (selectedDates, dateStr) => {
            emit('update:modelValue', dateStr)
        },
        onReady: (selectedDates, dateStr, instance) => {
            // Add dark mode class to calendar if needed
            if (appStore.darkMode && instance.calendarContainer) {
                instance.calendarContainer.classList.add('flatpickr-dark')
            }
        }
    })
}

onMounted(() => {
    flatpickrInstance = initFlatpickr()
})

// Watch for dark mode changes and update flatpickr
watch(() => appStore.darkMode, (isDark) => {
    if (flatpickrInstance && flatpickrInstance.calendarContainer) {
        if (isDark) {
            flatpickrInstance.calendarContainer.classList.add('flatpickr-dark')
        } else {
            flatpickrInstance.calendarContainer.classList.remove('flatpickr-dark')
        }
    }
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
            flatpickrInstance = initFlatpickr()
        }
    }
})
</script>

<style>
/* Dark mode styles for Flatpickr calendar */
.flatpickr-dark.flatpickr-calendar {
    background: #1f2937;
    border: 1px solid #374151;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
}

.flatpickr-dark .flatpickr-months {
    background: #1f2937;
    border-bottom: 1px solid #374151;
}

.flatpickr-dark .flatpickr-month {
    color: #e5e7eb;
}

.flatpickr-dark .flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-dark .flatpickr-current-month input.cur-year {
    background: #374151;
    color: #e5e7eb;
    border: 1px solid #4b5563;
}

.flatpickr-dark .flatpickr-current-month input.cur-year:hover,
.flatpickr-dark .flatpickr-monthDropdown-months:hover {
    background: #4b5563;
}

.flatpickr-dark .flatpickr-prev-month,
.flatpickr-dark .flatpickr-next-month {
    color: #9ca3af;
}

.flatpickr-dark .flatpickr-prev-month:hover svg,
.flatpickr-dark .flatpickr-next-month:hover svg {
    fill: #FF7B00;
}

.flatpickr-dark .flatpickr-weekdays {
    background: #1f2937;
}

.flatpickr-dark span.flatpickr-weekday {
    color: #9ca3af;
}

.flatpickr-dark .flatpickr-days {
    background: #1f2937;
}

.flatpickr-dark .flatpickr-day {
    color: #e5e7eb;
    border: 1px solid transparent;
}

.flatpickr-dark .flatpickr-day:hover:not(.flatpickr-disabled):not(.selected) {
    background: #374151;
    border-color: #4b5563;
}

.flatpickr-dark .flatpickr-day.today {
    border-color: #FF7B00;
    color: #FF7B00;
}

.flatpickr-dark .flatpickr-day.today:hover {
    background: rgba(255, 123, 0, 0.1);
    border-color: #FF7B00;
}

.flatpickr-dark .flatpickr-day.selected,
.flatpickr-dark .flatpickr-day.selected:hover {
    background: #FF7B00;
    border-color: #FF7B00;
    color: white;
}

.flatpickr-dark .flatpickr-day.inRange {
    background: rgba(255, 123, 0, 0.2);
    border-color: transparent;
}

.flatpickr-dark .flatpickr-day.disabled,
.flatpickr-dark .flatpickr-day.disabled:hover {
    color: #4b5563;
}

.flatpickr-dark .flatpickr-time {
    background: #1f2937;
    border-top: 1px solid #374151;
}

.flatpickr-dark .flatpickr-time input {
    background: #374151;
    color: #e5e7eb;
    border: 1px solid #4b5563;
}

.flatpickr-dark .flatpickr-time input:hover,
.flatpickr-dark .flatpickr-time input:focus {
    background: #4b5563;
}

.flatpickr-dark .flatpickr-time .flatpickr-time-separator,
.flatpickr-dark .flatpickr-time .flatpickr-am-pm {
    color: #9ca3af;
}

/* Light mode - use primary colors */
.flatpickr-calendar {
    border-color: #FFA040;
}

.flatpickr-day.today {
    border-color: #FF7B00;
    color: #FF7B00;
}

.flatpickr-day.today:hover {
    background: rgba(255, 123, 0, 0.1);
    border-color: #FF7B00;
}

.flatpickr-day.selected,
.flatpickr-day.selected:hover {
    background: #FF7B00;
    border-color: #FF7B00;
}

.flatpickr-day.inRange {
    background: rgba(255, 123, 0, 0.2);
}

.flatpickr-prev-month:hover svg,
.flatpickr-next-month:hover svg {
    fill: #FF7B00;
}
</style>
