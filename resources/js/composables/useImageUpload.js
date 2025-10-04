import { ref } from 'vue'
import api from '@/services/api'

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

    const upload = async (file, url = '/upload') => {
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

            const response = await api.upload(url, formData, (progressEvent) => {
                progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            })

            return response
        } catch (err) {
            error.value = err.message || 'Upload failed'
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
