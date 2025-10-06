import api from '../api'

export default {
    fetchAll: (params) => api.get('/admin/settings', params),
    updateBatch: (settings) => api.put('/admin/settings/batch', { settings }),
    getPublic: () => api.get('/settings/public'),
}
