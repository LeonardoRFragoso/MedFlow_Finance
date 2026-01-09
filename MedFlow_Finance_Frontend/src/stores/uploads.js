import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useUploadsStore = defineStore('uploads', () => {
  const uploads = ref([])
  const currentUpload = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchUploads = async (filters = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get('/uploads', { params: filters })
      uploads.value = response.data.data
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao carregar uploads'
      return false
    } finally {
      loading.value = false
    }
  }

  const fetchUpload = async (id) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get(`/uploads/${id}`)
      currentUpload.value = response.data.data
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao carregar upload'
      return false
    } finally {
      loading.value = false
    }
  }

  const uploadFile = async (file, billingPeriodStart, billingPeriodEnd, description = '') => {
    loading.value = true
    error.value = null

    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('billing_period_start', billingPeriodStart)
      formData.append('billing_period_end', billingPeriodEnd)
      formData.append('description', description)

      const response = await api.post('/uploads', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (progressEvent) => {
          const percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          )
          currentUpload.value = {
            ...currentUpload.value,
            uploadProgress: percentCompleted,
          }
        },
      })

      currentUpload.value = response.data.data
      await fetchUploads()
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao fazer upload'
      return false
    } finally {
      loading.value = false
    }
  }

  const getUploadStatus = async (id) => {
    try {
      const response = await api.get(`/uploads/${id}/status`)
      return response.data.data
    } catch (err) {
      console.error('Erro ao obter status:', err)
      return null
    }
  }

  const deleteUpload = async (id) => {
    try {
      await api.delete(`/uploads/${id}`)
      uploads.value = uploads.value.filter((u) => u.id !== id)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao deletar upload'
      return false
    }
  }

  return {
    uploads,
    currentUpload,
    loading,
    error,
    fetchUploads,
    fetchUpload,
    uploadFile,
    getUploadStatus,
    deleteUpload,
  }
})
