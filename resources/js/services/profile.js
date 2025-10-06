import api from './api'

export default {
    get: () => api.get('/profile'),
    update: (data) => api.put('/profile', data),
    changePassword: (data) => api.put('/profile/change-password', data),
}
