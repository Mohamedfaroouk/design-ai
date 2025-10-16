<template>
    <div class="mb-5">
        <label v-if="label" :for="id" class="block text-sm font-semibold mb-2"
               :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative group">
            <!-- Custom Select Dropdown -->
            <div class="relative">
                <button
                    ref="buttonRef"
                    type="button"
                    @click="toggleDropdown"
                    :disabled="disabled"
                    class="w-full px-4 py-3 pe-10 border-2 rounded-xl shadow-sm transition-all duration-200 cursor-pointer backdrop-blur-sm text-start disabled:cursor-not-allowed disabled:opacity-60"
                    :class="error
                        ? 'border-red-300 bg-red-50/50 dark:bg-red-900/20 dark:border-red-500 dark:text-gray-200'
                        : appStore.darkMode
                            ? 'border-gray-600 bg-gray-800 text-gray-200 hover:border-gray-500 disabled:hover:border-gray-600'
                            : 'border-primary-200 bg-white/50 hover:border-primary-300 focus:border-primary disabled:hover:border-primary-200'"
                >
                    <span v-if="selectedLabel" class="block truncate">{{ selectedLabel }}</span>
                    <span v-else :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'">{{ placeholder }}</span>
                </button>
                <!-- Dropdown Arrow Icon -->
                <div class="absolute end-3 top-1/2 -translate-y-1/2 pointer-events-none transition-transform duration-200"
                     :class="isOpen ? 'rotate-180' : 'rotate-0'">
                    <svg class="w-5 h-5 transition-colors"
                         :class="disabled
                             ? appStore.darkMode ? 'text-gray-600' : 'text-gray-400'
                             : appStore.darkMode ? 'text-gray-400 group-hover:text-primary-400' : 'text-gray-500 group-hover:text-primary-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <Teleport to="body">
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="isOpen"
                            ref="dropdownRef"
                            class="fixed z-[60] rounded-xl shadow-2xl overflow-hidden backdrop-blur-sm ring-1"
                            :style="dropdownStyle"
                            :class="appStore.darkMode
                                ? 'bg-gray-800 ring-gray-700'
                                : 'bg-white ring-primary-200/50'"
                        >
                            <!-- Search Input -->
                            <div
                                v-if="searchable"
                                class="p-2 border-b"
                                :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-100'"
                            >
                                <div class="relative">
                                    <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                                         :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search..."
                                        class="w-full ps-9 pe-3 py-2 rounded-lg focus:outline-none transition-all text-sm"
                                        :class="appStore.darkMode
                                            ? 'bg-gray-900 text-gray-200 placeholder-gray-500 focus:bg-gray-900/80 focus:ring-2 focus:ring-primary-500/50'
                                            : 'bg-gray-50 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-primary-500/50'"
                                        @click.stop
                                    />
                                </div>
                            </div>

                            <!-- Options List -->
                            <div class="max-h-60 overflow-y-auto py-1 scrollbar-thin scrollbar-thumb-rounded"
                                 :class="appStore.darkMode ? 'scrollbar-thumb-gray-600 scrollbar-track-gray-800' : 'scrollbar-thumb-gray-300 scrollbar-track-gray-100'">
                                <div
                                    v-for="option in filteredOptions"
                                    :key="option[valueKey]"
                                    @click="selectOption(option)"
                                    class="group relative px-4 py-2.5 cursor-pointer transition-all duration-150 flex items-center justify-between"
                                    :class="option[valueKey] === modelValue
                                        ? appStore.darkMode
                                            ? 'bg-primary-900/30 text-primary-300'
                                            : 'bg-primary-50 text-primary-700'
                                        : appStore.darkMode
                                            ? 'text-gray-300 hover:bg-gray-700/50'
                                            : 'text-gray-700 hover:bg-primary-50/50'"
                                >
                                    <!-- Option Label -->
                                    <span class="text-sm font-medium truncate"
                                          :class="option[valueKey] === modelValue ? 'font-semibold' : ''">
                                        {{ option[labelKey] }}
                                    </span>

                                    <!-- Selected Checkmark -->
                                    <svg
                                        v-if="option[valueKey] === modelValue"
                                        class="w-5 h-5 flex-shrink-0 ms-2 animate-in fade-in zoom-in duration-200"
                                        :class="appStore.darkMode ? 'text-primary-400' : 'text-primary-600'"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>

                                    <!-- Hover Indicator -->
                                    <div
                                        v-else
                                        class="w-5 h-5 flex-shrink-0 ms-2 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                                        :class="appStore.darkMode ? 'bg-gray-600' : 'bg-primary-200/50'"
                                    ></div>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-if="filteredOptions.length === 0"
                                    class="px-4 py-8 text-center"
                                >
                                    <svg class="w-12 h-12 mx-auto mb-2"
                                         :class="appStore.darkMode ? 'text-gray-600' : 'text-gray-300'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium"
                                       :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        No options found
                                    </p>
                                    <p class="text-xs mt-1"
                                       :class="appStore.darkMode ? 'text-gray-500' : 'text-gray-400'">
                                        Try a different search term
                                    </p>
                                </div>
                            </div>

                            <!-- Options Count Footer (if searchable) -->
                            <div
                                v-if="searchable && filteredOptions.length > 0"
                                class="px-3 py-2 border-t text-xs"
                                :class="appStore.darkMode
                                    ? 'border-gray-700 text-gray-500 bg-gray-900/50'
                                    : 'border-gray-100 text-gray-500 bg-gray-50/50'"
                            >
                                {{ filteredOptions.length }} of {{ computedOptions.length }} option{{ computedOptions.length !== 1 ? 's' : '' }}
                            </div>
                        </div>
                    </Transition>
                </Teleport>
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
import { ref, computed, watch, nextTick } from 'vue'
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
    options: {
        type: Array,
        default: () => []
    },
    valueKey: {
        type: String,
        default: 'value'
    },
    labelKey: {
        type: String,
        default: 'label'
    },
    placeholder: {
        type: String,
        default: 'Select an option...'
    },
    searchable: {
        type: Boolean,
        default: false
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
    }
})

const emit = defineEmits(['update:modelValue'])

const id = computed(() => `select-${Math.random().toString(36).substr(2, 9)}`)
const isOpen = ref(false)
const searchQuery = ref('')
const buttonRef = ref(null)
const dropdownRef = ref(null)
const dropdownStyle = ref({})

const computedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object') return opt
        return { [props.valueKey]: opt, [props.labelKey]: opt }
    })
})

const selectedLabel = computed(() => {
    const selected = computedOptions.value.find(opt => opt[props.valueKey] === props.modelValue)
    return selected ? selected[props.labelKey] : ''
})

const filteredOptions = computed(() => {
    if (!searchQuery.value) return computedOptions.value
    return computedOptions.value.filter(opt =>
        opt[props.labelKey].toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

const updateDropdownPosition = () => {
    if (!buttonRef.value) return

    const buttonRect = buttonRef.value.getBoundingClientRect()
    const viewportHeight = window.innerHeight
    const spaceBelow = viewportHeight - buttonRect.bottom
    const spaceAbove = buttonRect.top

    // Decide if dropdown should open above or below
    const openAbove = spaceBelow < 300 && spaceAbove > spaceBelow

    dropdownStyle.value = {
        left: `${buttonRect.left}px`,
        width: `${buttonRect.width}px`,
        top: openAbove ? 'auto' : `${buttonRect.bottom + 8}px`,
        bottom: openAbove ? `${viewportHeight - buttonRect.top + 8}px` : 'auto'
    }
}

const toggleDropdown = () => {
    if (!props.disabled) {
        isOpen.value = !isOpen.value
        if (isOpen.value) {
            nextTick(() => {
                updateDropdownPosition()
            })
        }
    }
}

const selectOption = (option) => {
    emit('update:modelValue', option[props.valueKey])
    isOpen.value = false
    searchQuery.value = ''
}

// Close dropdown when clicking outside
const closeDropdown = (event) => {
    if (
        !buttonRef.value?.contains(event.target) &&
        !dropdownRef.value?.contains(event.target)
    ) {
        isOpen.value = false
    }
}

// Update position on scroll/resize
const handlePositionUpdate = () => {
    if (isOpen.value) {
        updateDropdownPosition()
    }
}

watch(isOpen, (newValue) => {
    if (newValue) {
        document.addEventListener('click', closeDropdown)
        window.addEventListener('scroll', handlePositionUpdate, true)
        window.addEventListener('resize', handlePositionUpdate)
    } else {
        document.removeEventListener('click', closeDropdown)
        window.removeEventListener('scroll', handlePositionUpdate, true)
        window.removeEventListener('resize', handlePositionUpdate)
    }
})
</script>
