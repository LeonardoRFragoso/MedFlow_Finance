import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const clinic = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  const login = async (email, password) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/auth/login', { email, password })
      const { data } = response.data

      token.value = data.token
      user.value = data.user
      clinic.value = data.clinic

      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      localStorage.setItem('clinic', JSON.stringify(data.clinic))

      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao fazer login'
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout')
    } catch (err) {
      console.error('Erro ao fazer logout:', err)
    } finally {
      token.value = null
      user.value = null
      clinic.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      localStorage.removeItem('clinic')
    }
  }

  const loadFromStorage = () => {
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('user')
    const storedClinic = localStorage.getItem('clinic')

    if (storedToken) {
      token.value = storedToken
      user.value = storedUser ? JSON.parse(storedUser) : null
      clinic.value = storedClinic ? JSON.parse(storedClinic) : null
    }
  }

  const hasPermission = (permission) => {
    if (!user.value) return false
    return user.value.permissions?.includes(permission) || false
  }

  const hasRole = (role) => {
    if (!user.value) return false
    return user.value.roles?.includes(role) || false
  }

  return {
    user,
    clinic,
    token,
    loading,
    error,
    isAuthenticated,
    login,
    logout,
    loadFromStorage,
    hasPermission,
    hasRole,
  }
})
