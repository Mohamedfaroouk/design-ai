import api from '@/services/api'

export default {
    /**
     * Upload an image file
     */
    uploadImage(file) {
        const formData = new FormData()
        formData.append('file', file)

        return api.upload('/client/uploads', formData)
    },

    /**
     * Generate AI image
     */
    generate(data) {
        return api.post('/client/ai/generate', data)
    },

    /**
     * Check job status
     */
    checkStatus(jobId) {
        return api.get(`/client/ai/status/${jobId}`)
    },
}
