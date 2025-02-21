import { ref } from 'vue'
import { useApi } from './useApi'
import type { User } from '~/types/user'

interface LoginCredentials {
  email: string
  password: string
}

interface AuthResponse {
  token_type: string
  expires_in: number
  access_token: string
  refresh_token: string
}

export const useAuth = () => {
  const api = useApi()
  const user = ref<User | null>(null)
  const isAuthenticated = ref(false)
  const token = ref<string | null>(null)
  const refreshToken = ref<string | null>(null)

  // Safely handle localStorage operations
  const storage = {
    setItem(key: string, value: string) {
      if (process.client) {
        localStorage.setItem(key, value)
      }
    },
    getItem(key: string) {
      if (process.client) {
        return localStorage.getItem(key)
      }
      return null
    },
    removeItem(key: string) {
      if (process.client) {
        localStorage.removeItem(key)
      }
    }
  }

  // Load tokens from storage on init
  const initAuth = () => {
    token.value = storage.getItem('access_token')
    refreshToken.value = storage.getItem('refresh_token')
    //user.value = storage.getItem('user')
    isAuthenticated.value = !!token.value
  }

  // Save tokens to storage
  const setTokens = (authResponse: AuthResponse) => {
    token.value = authResponse.access_token
    refreshToken.value = authResponse.refresh_token
    storage.setItem('access_token', authResponse.access_token)
    storage.setItem('refresh_token', authResponse.refresh_token)
    isAuthenticated.value = true
  }

  // Clear tokens from storage
  const clearTokens = () => {
    token.value = null
    refreshToken.value = null
    storage.removeItem('access_token')
    storage.removeItem('refresh_token')
    isAuthenticated.value = false
    user.value = null
  }

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await api.post<AuthResponse>('/user-auth/login', credentials)
      setTokens(response.original)
      await fetchUser()
      return response
    } catch (error) {
      clearTokens()
      throw error
    }
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout', {})
    } finally {
      clearTokens()
    }
  }

  const fetchUser = async () => {
    try {
      const response = await api.get<User>('/user-profile', undefined, { useAuth: true })
      user.value = response.data
      console.log('Fetched user:', response)
      storage.setItem('user', user.value)
      return response
    } catch (error) {
      clearTokens()
      throw error
    }
  }

  const refreshAuth = async () => {
    if (!refreshToken.value) {
      throw new Error('No refresh token available')
    }

    try {
      const response = await api.post<AuthResponse>('/user-auth/refresh-token', {
        refresh_token: refreshToken.value
      })
      setTokens(response.original)
      return response
    } catch (error) {
      clearTokens()
      throw error
    }
  }

  // Initialize auth state
  initAuth()

  return {
    user,
    isAuthenticated,
    token,
    login,
    logout,
    fetchUser,
    refreshAuth
  }
}