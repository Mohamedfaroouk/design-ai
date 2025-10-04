<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div
                @click="toggleDropdown"
                class="w-full min-h-[42px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer bg-white"
                :class="{ 'border-red-500': error }"
            >
                <div v-if="selectedLabels.length > 0" class="flex flex-wrap gap-1">
                    <span
                        v-for="(label, index) in selectedLabels"
                        :key="index"
                        class="inline-flex items-center gap-1 px-2 py-1 text-sm bg-primary-100 text-primary-800 rounded"
                    >
                        {{ label }}
                        <button
                            @click.stop="removeOption(modelValue[index])"
                            class="hover:text-primary-900"
                        >
                            ×
                        </button>
                    </span>
                </div>
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
                    @click="toggleOption(option)"
                    class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100"
                >
                    <input
                        type="checkbox"
                        :checked="isSelected(option[valueKey])"
                        class="mr-2"
                        @click.stop
                    />
                    <span>{{ option[labelKey] }}</span>
                </div>
                <div v-if="filteredOptions.length === 0" class="px-3 py-2 text-gray-500">
                    No options found
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
        type: Array,
        default: () => []
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
        default: 'Select options...'
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

const id = computed(() => `multiselect-${Math.random().toString(36).substr(2, 9)}`)
const isOpen = ref(false)
const searchQuery = ref('')

const computedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object') return opt
        return { [props.valueKey]: opt, [props.labelKey]: opt }
    })
})

const selectedLabels = computed(() => {
    return props.modelValue.map(value => {
        const option = computedOptions.value.find(opt => opt[props.valueKey] === value)
        return option ? option[props.labelKey] : value
    })
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

const isSelected = (value) => {
    return props.modelValue.includes(value)
}

const toggleOption = (option) => {
    const value = option[props.valueKey]
    const newValue = [...props.modelValue]
    const index = newValue.indexOf(value)

    if (index > -1) {
        newValue.splice(index, 1)
    } else {
        newValue.push(value)
    }

    emit('update:modelValue', newValue)
}

const removeOption = (value) => {
    const newValue = props.modelValue.filter(v => v !== value)
    emit('update:modelValue', newValue)
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
        searchQuery.value = ''
    }
})
</script>
