import type { ApiResponse } from "~/types/api"

interface RequestOptions {
  useAuth?: boolean;
  customHeaders?: Record<string, string>;
}

export const useApi = () => {
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase

    const getHeaders = (options?: RequestOptions) => {
        const headers: Record<string, string> = {
            'Accept': 'application/json',
            ...options?.customHeaders
        }

        if (options?.useAuth) {
            const token = localStorage.getItem('access_token')
            if (token) {
                headers.Authorization = `Bearer ${token}`
            }
        }

        return headers
    }

    const get = async <T>(
        endpoint: string, 
        params?: Record<string, any>,
        options?: RequestOptions
    ): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'GET',
            headers: getHeaders(options)
        })
    }

    const post = async <T>(
        endpoint: string, 
        body: any, 
        params?: Record<string, any>,
        options?: RequestOptions
    ): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'POST',
            body,
            headers: getHeaders(options)
        })
    }

    const put = async <T>(
        endpoint: string, 
        body: any, 
        params?: Record<string, any>,
        options?: RequestOptions
    ): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'PUT',
            body,
            headers: getHeaders(options)
        })
    }

    const del = async <T>(
        endpoint: string, 
        params?: Record<string, any>,
        options?: RequestOptions
    ): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'DELETE',
            headers: getHeaders(options)
        })
    }

    return {
        get,
        post,
        put,
        del,
    }
}