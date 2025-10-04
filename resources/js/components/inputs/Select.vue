<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <select
                v-if="!searchable"
                :id="id"
                :value="modelValue"
                @change="$emit('update:modelValue', $event.target.value)"
                :disabled="disabled"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed appearance-none"
                :class="{ 'border-red-500': error }"
            >
                <option value="" v-if="placeholder">{{ placeholder }}</option>
                <option
                    v-for="option in computedOptions"
                    :key="option[valueKey]"
                    :value="option[valueKey]"
                >
                    {{ option[labelKey] }}
                </option>
            </select>

            <!-- Searchable Select -->
            <div v-else>
                <div
                    @click="toggleDropdown"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer bg-white"
                    :class="{ 'border-red-500': error }"
                >
                    <span v-if="selectedLabel">{{ selectedLabel }}</span>
                    <span v-else class="text-gray-400">{{ placeholder }}</span>
                </div>

                <div
                    v-if="isOpen"
                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                >
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search..."
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none"
                        @click.stop
                    />
                    <div
                        v-for="option in filteredOptions"
                        :key="option[valueKey]"
                        @click="selectOption(option)"
                        class="px-3 py-2 cursor-pointer hover:bg-gray-100"
                        :class="{ 'bg-primary-50': option[valueKey] === modelValue }"
                    >
                        {{ option[labelKey] }}
                    </div>
                    <div v-if="filteredOptions.length === 0" class="px-3 py-2 text-gray-500">
                        No options found
                    </div>
                </div>
            </div>
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

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

const toggleDropdown = () => {
    if (!props.disabled) {
        isOpen.value = !isOpen.value
    }
}

const selectOption = (option) => {
    emit('update:modelValue', option[props.valueKey])
    isOpen.value = false
    searchQuery.value = ''
}

// Close dropdown when clicking outside
const closeDropdown = (event) => {
    if (!event.target.closest('.relative')) {
        isOpen.value = false
    }
}

watch(isOpen, (newValue) => {
    if (newValue) {
        document.addEventListener('click', closeDropdown)
    } else {
        document.removeEventListener('click', closeDropdown)
    }
})
</script>
