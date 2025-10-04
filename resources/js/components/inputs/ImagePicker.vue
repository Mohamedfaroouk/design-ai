<template>
    <div class="mb-4">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="flex gap-4">
            <!-- Preview -->
            <div
                v-if="previewUrl"
                class="relative w-32 h-32 border-2 border-gray-300 rounded-md overflow-hidden"
            >
                <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover" />
                <button
                    @click="clearImage"
                    class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700"
                >
                    ×
                </button>
            </div>

            <!-- Upload area -->
            <div
                @click="triggerFileInput"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
                class="flex-1 px-4 py-6 border-2 border-dashed border-gray-300 rounded-md cursor-pointer hover:border-primary-500 transition-colors"
                :class="{
                    'border-primary-500 bg-primary-50': isDragging,
                    'border-red-500': error
                }"
            >
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
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
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                </div>
            </div>
        </div>

        <!-- Upload progress -->
        <div v-if="uploading" class="mt-2">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                    class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: `${uploadProgress}%` }"
                ></div>
            </div>
            <p class="mt-1 text-sm text-gray-600">Uploading... {{ uploadProgress }}%</p>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useImageUpload } from '@/composables/useImageUpload'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    uploadUrl: {
        type: String,
        default: '/api/upload'
    },
    autoUpload: {
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

const emit = defineEmits(['update:modelValue', 'upload:success', 'upload:error'])

const fileInput = ref(null)
const isDragging = ref(false)
const previewUrl = ref(props.modelValue)
const uploading = ref(false)
const uploadProgress = ref(0)

const { preview, handleFileSelect, upload } = useImageUpload()

const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleFileChange = async (event) => {
    const file = event.target.files?.[0]
    if (file) {
        handleFileSelect(file)
        if (props.autoUpload) {
            await uploadImage(file)
        } else {
            emit('update:modelValue', file)
        }
    }
}

const handleDrop = async (event) => {
    isDragging.value = false
    const file = event.dataTransfer.files?.[0]
    if (file && file.type.startsWith('image/')) {
        handleFileSelect(file)
        if (props.autoUpload) {
            await uploadImage(file)
        } else {
            emit('update:modelValue', file)
        }
    }
}

const uploadImage = async (file) => {
    try {
        uploading.value = true
        uploadProgress.value = 0

        const response = await upload(file, props.uploadUrl)

        previewUrl.value = response.url || response.path
        emit('update:modelValue', response.url || response.path)
        emit('upload:success', response)
    } catch (error) {
        emit('upload:error', error)
    } finally {
        uploading.value = false
        uploadProgress.value = 0
    }
}

const clearImage = () => {
    previewUrl.value = ''
    preview.value = null
    emit('update:modelValue', '')
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

watch(preview, (newValue) => {
    if (newValue) {
        previewUrl.value = newValue
    }
})

watch(() => props.modelValue, (newValue) => {
    if (newValue && typeof newValue === 'string') {
        previewUrl.value = newValue
    }
})
</script>
