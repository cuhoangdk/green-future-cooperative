import { AuthType, useApi } from './useApi'
import type { Customer } from '../types/customer'
import type { LoginResponse } from '../types/auth'

export const useCustomerAuth = () => {
    const { get, post, put } = useApi()

    const accessToken = useCookie('customer_access_token', { maxAge: 7200 })
    const refreshToken = useCookie('customer_refresh_token', { maxAge: 30 * 24 * 60 * 60 })
    const currentCustomer = useCookie<Customer | null>('customer_current', {
        maxAge: 30 * 24 * 60 * 60,
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

    const register = async (
        email: string,
        full_name: string,
        phone_number: string,
        password: string,
        password_confirmation: string
    ): Promise<{ success: true } | never> => {
        const { error, status } = await post<LoginResponse>(
            '/customer-auth/register',
            {
                email,
                full_name,
                phone_number,
                password,
                password_confirmation,
            },
            { authType: AuthType.Guest }
        )

        if (status.value === 'success') {
            return { success: true }
        }

        throw new Error(JSON.stringify({
            message: error.value?.message || 'Đăng kí thất bại',
            errors: error.value || {},
        }))
    }

    const verifyAccount = async (
        email: string,
        token: string
    ): Promise<{ success: true } | never> => {
        const { data, error, status } = await post<LoginResponse>(
            '/customer-auth/verify-account',
            { email, token },
            { authType: AuthType.Guest }
        )

        if (status.value === 'success' && data.value) {
            return { success: true }
        }

        throw new Error(JSON.stringify({
            message: data.value?.message || error.value?.message || 'Xác thực tài khoản thất bại',
            errors: data.value?.errors || {},
            statusCode: error.value?.statusCode, // Thêm statusCode vào lỗi
        }))
    }

    const forgotPassword = async (email: string): Promise<{ success: true } | never> => {
        const { status } = await post<LoginResponse>(
            '/customer-auth/forgot-password',
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
            '/customer-auth/reset-password',
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

    const login = async (email: string, password: string) => {
        const { data, error } = await post<LoginResponse>('/customer-auth/login', {
            email,
            password,
        }, { authType: AuthType.Guest })

        if (data.value && data.value.original.access_token) {
            accessToken.value = data.value.original.access_token
            refreshToken.value = data.value.original.refresh_token
            return { success: true }
        } else {
            throw new Error(data.value?.original.message || 'Đăng nhập thất bại')
        }
    }

    const logout = async () => {
        await post('/customer-auth/logout', { authType: AuthType.Customer })
        accessToken.value = null
        refreshToken.value = null
        currentCustomer.value = null
    }

    const fetchCurrentCustomer = async () => {
        if (!accessToken.value) {
            currentCustomer.value = null
            return null
        }

        const { data, error } = await get<Customer>('/customer-profile', { authType: AuthType.Customer })
        if (data.value && !error.value) {
            currentCustomer.value = data.value.data
            return data.value
        }
        // else {
        //     if (error.value?.statusCode === 401 && refreshToken.value) {
        //         await refreshAccessToken()
        //         return await fetchCurrentCustomer()
        //     }
        //     currentCustomer.value = null
        //     return null
        // }
    }

    const refreshAccessToken = async () => {
        if (!refreshToken.value) {
            logout()
            return false
        }

        const { data, error } = await post<LoginResponse>('/customer-auth/refresh-token', {
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
            throw new Error('Customer not authenticated')
        }

        const { data, status, error } = await put<Customer>('/customer-profile', profileData, {
            authType: AuthType.Customer
        })

        if (data.value && !error.value) {
            currentCustomer.value = {
                ...currentCustomer.value,
                ...data.value.data
            }
            return {
                success: true,
                customer: currentCustomer.value,
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

    const changePassword = async (
        passwordData: { current_password: string; new_password: string; new_password_confirmation: string },
        authType: AuthType = AuthType.Customer
    ) => {
        return await put<null>(`/customer-auth/change-password`, passwordData, {
            authType,
        });
    };


    const isAuthenticated = computed(() => !!accessToken.value)

    return {
        login,
        logout,
        fetchCurrentCustomer,
        refreshAccessToken,
        updateProfile,
        register,
        verifyAccount,
        forgotPassword,
        resetPassword,
        changePassword,
        currentCustomer: readonly(currentCustomer),
        accessToken: readonly(accessToken),
        refreshToken: readonly(refreshToken),
        isAuthenticated,
    }
}
