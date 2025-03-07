import type { ApiResponse } from '../types/api'

// Định nghĩa enum cho authType
export enum AuthType {
  User = 'user',
  Customer = 'customer',
  Guest = 'guest',
}

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
      authType = AuthType.User, // Mặc định là 'user'
      customHeaders = {} as Record<string, string>,
      lazy = true, // Mặc định lazy là true
    } = options as {
      body?: any
      params?: Record<string, any>
      authType?: AuthType
      customHeaders?: Record<string, string>
      lazy?: boolean
    }

    // Lấy token dựa trên authType
    let token: string | null = null
    if (import.meta.client) {
      if (authType === AuthType.User || authType === AuthType.Customer) {
        token = useCookie('user_access_token', { maxAge: 7200 }).value || null
      }
    }

    const headers: Record<string, string> = {
      ...(body instanceof FormData ? {} : { 'Content-Type': 'application/json' }), // Chỉ thêm nếu không phải FormData
      ...customHeaders,
    }

    if (token && authType !== AuthType.Guest) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const url = `${baseURL}${endpoint}`
    const { data, status, error, refresh } = await useAsyncData(
      `${method}-${endpoint}`,
      () =>
        $fetch<ApiResponse<T>>(url, {
          method,
          headers,
          body: body instanceof FormData ? body : body ? JSON.stringify(body) : undefined,
          query: params,
        }),
      { lazy }
    )

    return { data, status, error, refresh }
  }

  return {
    get: <T>(endpoint: string, options = {}) => apiFetch<T>('GET', endpoint, options),
    post: <T>(endpoint: string, body: any, options = {}) => apiFetch<T>('POST', endpoint, { body, ...options }),
    put: <T>(endpoint: string, body: any, options = {}) => {
      // Chuyển PUT thành POST với _method=PUT
      const updatedBody = body instanceof FormData
        ? body // Nếu là FormData, thêm trực tiếp vào FormData
        : { ...body, _method: 'PUT' } // Nếu là object, thêm _method vào body
      
      if (body instanceof FormData) {
        updatedBody.append('_method', 'PUT')
      }

      return apiFetch<T>('POST', endpoint, { body: updatedBody, ...options })
    },
    del: <T>(endpoint: string, options = {}) => apiFetch<T>('DELETE', endpoint, options),
  }
}