import type { ApiResponse } from "~/types/api"

export const useApi = () => {
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase

    const getHeaders = () => ({
        Authorization: `Bearer ${config.public.accessToken}`,
        'Accept': 'application/json'
    })

    const get = async <T>(endpoint: string, params?: Record<string, any>): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'GET',
            headers: getHeaders()
        })
    }

    const post = async <T>(endpoint: string, body: any, params?: Record<string, any>): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'POST',
            body,
            headers: getHeaders()
        })
    }

    const put = async <T>(endpoint: string, body: any, params?: Record<string, any>): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'PUT',
            body,
            headers: getHeaders()
        })
    }

    const del = async <T>(endpoint: string, params?: Record<string, any>): Promise<ApiResponse<T>> => {
        const url = new URL(`${baseUrl}${endpoint}`)
        if (params) {
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]))
        }
        return await $fetch<ApiResponse<T>>(url.toString(), {
            method: 'DELETE',
            headers: getHeaders()
        })
    }

    return {
        get,
        post,
        put,
        del,
    }
}