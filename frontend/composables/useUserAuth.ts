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
    encode: (value) => {
      if (!value) return ''
      // Mã hóa JSON thành Base64 để tránh vấn đề Unicode
      return btoa(encodeURIComponent(JSON.stringify(value)))
    },
    decode: (value) => {
      if (!value) return null
      try {
        // Giải mã Base64 và parse JSON
        return JSON.parse(decodeURIComponent(atob(value)))
      } catch (e) {
        console.error('Failed to decode currentUser cookie', e)
        return null
      }
    },
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

  const forgotPassword = async (email: string): Promise<{ success: true } | never> => {
    const { status } = await post<LoginResponse>(
      '/user-auth/forgot-password',
      { email },
      { authType: AuthType.Guest }
    )

    if (status.value === 'success') {
      return { success: true }
    }

    throw new Error('Email không tồn tại')
  }

  const resetPassword = async (
    email: string,
    token: string,
    password: string,
    password_confirmation: string
  ): Promise<{ success: true } | never> => {
    const { data, error, status } = await post<LoginResponse>(
      '/user-auth/reset-password',
      {
        email,
        token,
        password,
        password_confirmation,
      },
      { authType: AuthType.Guest }
    )

    if (status.value === 'success' && data.value) {
      return { success: true }
    }

    throw new Error('Đổi mật khẩu thất bại')

  }

  const logout = async () => {
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
      currentUser.value = data.value.data
      return data.value
    }
    //  else {
    //   if (error.value?.statusCode === 401 && refreshToken.value) {
    //     await refreshAccessToken()
    //     return await fetchCurrentUser()
    //   }
    //   currentUser.value = null
    //   return null
    // }
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

  // Đổi mật khẩu người dùng
  const changePassword = async (
    passwordData: { current_password: string; new_password: string; new_password_confirmation: string },
    authType: AuthType = AuthType.User
  ) => {
    return await put<null>(`/user-auth/change-password`, passwordData, {
      authType,
    });
  };


  const isAuthenticated = computed(() => !!accessToken.value)

  return {
    login,
    logout,
    fetchCurrentUser,
    refreshAccessToken,
    updateProfile,
    forgotPassword,
    resetPassword,
    changePassword,
    currentUser: readonly(currentUser),
    accessToken: readonly(accessToken),
    refreshToken: readonly(refreshToken),
    isAuthenticated,
  }
}
