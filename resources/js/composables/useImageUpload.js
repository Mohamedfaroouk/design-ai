import { ref } from 'vue'
import axios from 'axios'

export function useImageUpload() {
    const preview = ref(null)
    const uploading = ref(false)
    const progress = ref(0)
    const error = ref(null)

    const handleFileSelect = (file) => {
        if (!file) return

        // Validate file type
        if (!file.type.startsWith('image/')) {
            error.value = 'Please select an image file'
            return
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            error.value = 'Image size must be less than 5MB'
            return
        }

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            preview.value = e.target.result
        }
        reader.readAsDataURL(file)

        error.value = null
    }

    const upload = async (file, url = '/client/uploads') => {
        if (!file) {
            error.value = 'No file selected'
            return null
        }

        uploading.value = true
        progress.value = 0
        error.value = null

        try {
            const formData = new FormData()
            formData.append('file', file)

            // Use axios directly for file uploads with progress tracking
            const response = await axios.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                    }
                },
            })

            return response.data
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'Upload failed'
            throw err
        } finally {
            uploading.value = false
        }
    }

    const clearPreview = () => {
        preview.value = null
        progress.value = 0
        error.value = null
    }

    return {
        preview,
        uploading,
        progress,
        error,
        handleFileSelect,
        upload,
        clearPreview
    }
}
