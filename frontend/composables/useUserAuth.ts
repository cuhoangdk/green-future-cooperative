import { AuthType, useApi } from './useApi'
import type { ApiResponse } from '../types/api'
import type { User } from '../types/user'
import type { LoginResponse } from '../types/auth'

export const useUserAuth = () => {
  const { get, post, put } = useApi()

  // Sử dụng cookie để lưu trữ token và user
  const accessToken = useCookie('user_access_token', { maxAge: 7200 }) // 2 giờ
  const refreshToken = useCookie('user_refresh_token', { maxAge: 30 * 24 * 60 * 60 }) // 30 ngày
  const currentUser = useCookie<User | null>('user_current', {
    maxAge: 7200,
    default: () => null,
    encode: (value) => JSON.stringify(value),
    decode: (value) => (value ? JSON.parse(value) : null),
  })

  // Đăng nhập
  const login = async (email: string, password: string) => {
    const { data, error } = await post<LoginResponse>('/user-auth/login', {
      email,
      password,
    }, { authType: AuthType.Guest }) // Không cần token để đăng nhập

    if (data.value && !error.value) {
      // Lưu token và thông tin vào cookie
      accessToken.value = data.value.original.access_token
      refreshToken.value = data.value.original.refresh_token

      // Lấy thông tin người dùng ngay sau khi đăng nhập
      await fetchCurrentUser()
      return { success: true }
    } else {
      throw new Error(error.value?.message || 'Login failed')
    }
  }

  // Đăng xuất
  const logout = () => {
    // Xóa token và user khỏi cookie
    accessToken.value = null
    refreshToken.value = null
    currentUser.value = null
  }

  // Lấy thông tin người dùng hiện tại
  const fetchCurrentUser = async () => {
    if (!accessToken.value) {
      currentUser.value = null
      return null
    }

    const { data, error } = await get<User>('/user-profile', { authType: AuthType.User })
    if (data.value && !error.value) {
      currentUser.value = data.value.data
      return data.value
    } else {
      // Nếu lỗi (ví dụ token hết hạn), thử refresh token
      if (error.value?.statusCode === 401 && refreshToken.value) {
        await refreshAccessToken()
        return await fetchCurrentUser() // Thử lại sau khi refresh
      }
      currentUser.value = null
      return null
    }
  }

  // Làm mới access token
  const refreshAccessToken = async () => {
    if (!refreshToken.value) {
      logout()
      return false
    }

    const { data, error } = await post<LoginResponse>('/user-auth/refresh-token', {
      refresh_token: refreshToken.value,
      AuthType: AuthType.Guest, // Không cần token để refresh token
    })

    if (data.value && !error.value) {
      accessToken.value = data.value.original.access_token // Cập nhật access token nếu API trả về
      refreshToken.value = data.value.original.refresh_token // Cập nhật refresh token nếu API trả về
      return true
    } else {
      logout()
      throw new Error(error.value?.message || 'Refresh token failed')
    }
  }

  // Cập nhật profile người dùng
  const updateProfile = async (profileData: FormData) => {
    if (!accessToken.value) {
      throw new Error('User not authenticated')
    }

    const { data, status , error } = await put<User>('/user-profile', profileData, {
      authType: AuthType.User // Yêu cầu quyền user đã đăng nhập
    })

    if (data.value && !error.value) {
      // Cập nhật thông tin user trong cookie
      currentUser.value = {
        ...currentUser.value, // Giữ các thuộc tính cũ
        ...data.value.data    // Ghi đè với dữ liệu mới từ response
      }
      return {
        success: true,
        user: currentUser.value,
        status: status
      }
    } else {
      // Nếu token hết hạn, thử refresh rồi gọi lại
      if (error.value?.statusCode === 401 && refreshToken.value) {
        await refreshAccessToken()
        return await updateProfile(profileData) // Thử lại sau khi refresh
      }
      throw new Error(error.value?.message || 'Update profile failed')
    }
  }

  // Kiểm tra trạng thái đăng nhập
  const isAuthenticated = computed(() => !!accessToken.value)

  return {
    login,
    logout,
    fetchCurrentUser,
    refreshAccessToken,
    updateProfile,
    currentUser: readonly(currentUser), // Chỉ đọc để tránh sửa trực tiếp
    accessToken: readonly(accessToken),
    refreshToken: readonly(refreshToken),
    isAuthenticated,
  }
}