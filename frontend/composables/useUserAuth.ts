import { AuthType, useApi } from './useApi'
import type { ApiResponse } from '../types/api'
import type { User } from '../types/user'
import type { LoginResponse } from '../types/auth'

export const useUserAuth = () => {
  const { get, post, put } = useApi()

  const accessToken = useCookie('user_access_token', { maxAge: 7200 })
  const refreshToken = useCookie('user_refresh_token', { maxAge: 30 * 24 * 60 * 60 })
  const currentUser = useCookie<User | null>('user_current', {
    maxAge: 7200,
    default: () => null,
    encode: (value) => JSON.stringify(value),
    decode: (value) => (value ? JSON.parse(value) : null),
  })

  const login = async (email: string, password: string) => {
    const { data, error } = await post<LoginResponse>('/user-auth/login', {
      email,
      password,
    }, { authType: AuthType.Guest })

    if (data.value && data.value.original.access_token) {
      accessToken.value = data.value.original.access_token
      refreshToken.value = data.value.original.refresh_token
      return { success: true }
    } else {
      throw new Error(error.value?.message || 'Login failed')
    }
  }

  const logout = async() => {
    await post('/user-auth/logout', { authType: AuthType.User })
    accessToken.value = null
    refreshToken.value = null
    currentUser.value = null
  }

  const fetchCurrentUser = async () => {
    if (!accessToken.value) {
      currentUser.value = null
      return null
    }
  
    const { data, error } = await get<User>('/user-profile', { authType: AuthType.User })
    if (data.value && !error.value) {
      const userData = data.value.data || data.value
      console.log('Raw user data:', userData) // Log dữ liệu thô
  
      // Lọc dữ liệu để loại bỏ các giá trị không hợp lệ
      const sanitizedUserData = JSON.parse(JSON.stringify(userData, (key, value) => {
        if (typeof value === 'function' || value === undefined) return null
        return value
      }))
  
      try {
        currentUser.value = sanitizedUserData
        console.log('Stored in cookie:', currentUser.value) // Xác nhận lưu thành công
        return sanitizedUserData
      } catch (e) {
        console.error('Error storing user in cookie:', e)
        currentUser.value = null
        return null
      }
    } else {
      if (error.value?.statusCode === 401 && refreshToken.value) {
        await refreshAccessToken()
        return await fetchCurrentUser()
      }
      currentUser.value = null
      return null
    }
  }

  const refreshAccessToken = async () => {
    if (!refreshToken.value) {
      logout()
      return false
    }

    const { data, error } = await post<LoginResponse>('/user-auth/refresh-token', {
      refresh_token: refreshToken.value,
      AuthType: AuthType.Guest,
    })

    if (data.value && !error.value) {
      accessToken.value = data.value.original.access_token
      refreshToken.value = data.value.original.refresh_token
      return true
    } else {
      logout()
      throw new Error(error.value?.message || 'Refresh token failed')
    }
  }

  const updateProfile = async (profileData: FormData) => {
    if (!accessToken.value) {
      throw new Error('User not authenticated')
    }

    const { data, status, error } = await put<User>('/user-profile', profileData, {
      authType: AuthType.User
    })

    if (data.value && !error.value) {
      currentUser.value = {
        ...currentUser.value,
        ...data.value.data
      }
      return {
        success: true,
        user: currentUser.value,
        status: status
      }
    } else {
      if (error.value?.statusCode === 401 && refreshToken.value) {
        await refreshAccessToken()
        return await updateProfile(profileData)
      }
      throw new Error(error.value?.message || 'Update profile failed')
    }
  }

  const isAuthenticated = computed(() => !!accessToken.value)

  return {
    login,
    logout,
    fetchCurrentUser,
    refreshAccessToken,
    updateProfile,
    currentUser: readonly(currentUser),
    accessToken: readonly(accessToken),
    refreshToken: readonly(refreshToken),
    isAuthenticated,
  }
}
