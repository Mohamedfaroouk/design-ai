import api from '../api'

export default {
  fetchList(params = {}) {
    return api.get('/client/products', params)
  },

  fetchOne(id) {
    return api.get(`/client/products/${id}`)
  },

  create(data) {
    return api.post('/client/products', data)
  },

  update(id, data) {
    return api.put(`/client/products/${id}`, data)
  },

  delete(id) {
    return api.delete(`/client/products/${id}`)
  }
}
