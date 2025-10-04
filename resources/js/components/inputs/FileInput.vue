<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div
            @click="triggerFileInput"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-md cursor-pointer hover:border-primary-500 transition-colors"
            :class="{
                'border-primary-500 bg-primary-50': isDragging,
                'border-red-500': error
            }"
        >
            <input
                :id="id"
                ref="fileInput"
                type="file"
                :accept="accept"
                :multiple="multiple"
                @change="handleFileChange"
                class="hidden"
            />
            <div class="text-center">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 48 48"
                >
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                <p class="mt-2 text-sm text-gray-600">
                    <span class="font-medium text-primary-600">Click to upload</span>
                    or drag and drop
                </p>
                <p v-if="accept" class="mt-1 text-xs text-gray-500">{{ accept }}</p>
            </div>
        </div>

        <!-- Selected files list -->
        <div v-if="selectedFiles.length > 0" class="mt-2 space-y-2">
            <div
                v-for="(file, index) in selectedFiles"
                :key="index"
                class="flex items-center justify-between p-2 bg-gray-50 rounded"
            >
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="text-sm text-gray-700">{{ file.name }}</span>
                    <span class="text-xs text-gray-500">({{ formatFileSize(file.size) }})</span>
                </div>
                <button
                    @click.stop="removeFile(index)"
                    class="text-red-600 hover:text-red-800"
                >
                    ×
                </button>
            </div>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [File, Array],
        default: null
    },
    label: {
        type: String,
        default: ''
    },
    accept: {
        type: String,
        default: ''
    },
    multiple: {
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
    }
})

const emit = defineEmits(['update:modelValue'])

const id = computed(() => `file-${Math.random().toString(36).substr(2, 9)}`)
const fileInput = ref(null)
const isDragging = ref(false)
const selectedFiles = ref([])

const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleFileChange = (event) => {
    const files = Array.from(event.target.files || [])
    updateFiles(files)
}

const handleDrop = (event) => {
    isDragging.value = false
    const files = Array.from(event.dataTransfer.files || [])
    updateFiles(files)
}

const updateFiles = (files) => {
    if (props.multiple) {
        selectedFiles.value = files
        emit('update:modelValue', files)
    } else {
        selectedFiles.value = files.slice(0, 1)
        emit('update:modelValue', files[0] || null)
    }
}

const removeFile = (index) => {
    selectedFiles.value.splice(index, 1)
    if (props.multiple) {
        emit('update:modelValue', selectedFiles.value)
    } else {
        emit('update:modelValue', null)
    }
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}
</script>
