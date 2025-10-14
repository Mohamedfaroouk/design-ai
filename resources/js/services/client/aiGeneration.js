import api from '../api'

export default {
  /**
   * Fetch AI generation jobs list
   */
  fetchList(params = {}) {
    return api.get('/client/ai-generations', params)
  },

  /**
   * Retry a failed AI generation job
   */
  retry(id) {
    return api.post(`/client/ai-generations/${id}/retry`)
  },

  /**
   * Download generated image
   */
  async download(id) {
    // Use axios directly for blob download since api wrapper doesn't support responseType
    const axios = (await import('axios')).default
    const token = localStorage.getItem('auth_token')
    const response = await axios.get(`/api/client/ai-generations/${id}/download`, {
      responseType: 'blob',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    return response
  },
}
