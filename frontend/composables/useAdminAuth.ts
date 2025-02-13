import { useApi } from './useApi'
import type { PostCategory } from '~/types/postcategory'
import type { ApiResponse } from '~/types/api'

export const usePostcategories = () => {
    const { get, post, put, del } = useApi()
    const login = async (credentials: { username: string; password: string }): Promise<ApiResponse> => {
        try {
            const response = await post('/auth/login', credentials)
            return response.data
        } catch (error) {
            throw new Error('Login failed')
        }
    }

    return {
        login
    }
}