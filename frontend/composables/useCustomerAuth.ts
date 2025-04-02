import { AuthType, useApi } from './useApi'
import type { Customer } from '../types/customer'
import type { LoginResponse } from '../types/auth'

export const useCustomerAuth = () => {
    const { get, post, put } = useApi()

    const accessToken = useCookie('customer_access_token', { maxAge: 7200 })
    const refreshToken = useCookie('customer_refresh_token', { maxAge: 30 * 24 * 60 * 60 })
    const currentCustomer = useCookie<Customer | null>('customer_current', {
        maxAge: 7200,
        default: () => null,
        encode: (value) => JSON.stringify(value),
        decode: (value) => (value ? JSON.parse(value) : null),
    })

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
            throw new Error(error.value?.message || 'Login failed')
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
        } else {
            if (error.value?.statusCode === 401 && refreshToken.value) {
                await refreshAccessToken()
                return await fetchCurrentCustomer()
            }
            currentCustomer.value = null
            return null
        }
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

    const isAuthenticated = computed(() => !!accessToken.value)

    return {
        login,
        logout,
        fetchCurrentCustomer,
        refreshAccessToken,
        updateProfile,
        currentCustomer: readonly(currentCustomer),
        accessToken: readonly(accessToken),
        refreshToken: readonly(refreshToken),
        isAuthenticated,
    }
}
