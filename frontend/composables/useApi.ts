import type { ApiResponse } from '../types/api'

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBaseUrl || 'http://localhost:3000/api'

  const apiFetch = async <T>(
    method: 'GET' | 'HEAD' | 'PATCH' | 'POST' | 'PUT' | 'DELETE' | 'CONNECT' | 'OPTIONS' | 'TRACE' | 'get' | 'head' | 'patch' | 'post' | 'put' | 'delete' | 'connect' | 'options' | 'trace',
    endpoint: string,
    options = {}
  ) => {
    const {
      body = null,
      params = {} as Record<string, any>,
      useToken = true,
      customHeaders = {} as Record<string, string>,
      lazy = true, // Mặc định lazy là true
    } = options as {
      body?: any
      params?: Record<string, any>
      useToken?: boolean
      customHeaders?: Record<string, string>
      lazy?: boolean // Thêm tùy chọn lazy
    }

    // Chỉ truy cập localStorage ở phía client
    const token = import.meta.client ? localStorage.getItem('auth_token') : null
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      ...customHeaders,
    }

    if (useToken && token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const url = `${baseURL}${endpoint}`
    const { data, status, error, refresh } = await useAsyncData(
      `${method}-${endpoint}`,
      () =>
        $fetch<ApiResponse<T>>(url, {
          method,
          headers,
          body: body ? JSON.stringify(body) : undefined,
          query: params,
        }),
      { lazy } // Truyền tùy chọn lazy
    )

    return { data, status, error, refresh }
  }

  return {
    get: <T>(endpoint: string, options = {}) => apiFetch<T>('GET', endpoint, options),
    post: <T>(endpoint: string, body: any, options = {}) => apiFetch<T>('POST', endpoint, { body, ...options }),
    put: <T>(endpoint: string, body: any, options = {}) => apiFetch<T>('PUT', endpoint, { body, ...options }),
    del: <T>(endpoint: string, options = {}) => apiFetch<T>('DELETE', endpoint, options),
  }
}