import type { ApiResponse } from '../types/api'
import { watch } from 'vue';

// Định nghĩa enum cho authType
export enum AuthType {
  User = 'user',
  Customer = 'customer',
  Guest = 'guest',
}

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBaseUrl || 'http://localhost:3000/api'
  
  // Sử dụng useRequestHeaders để lấy cookie từ request khi ở server-side
  const apiFetch = async <T>(
    method: 'GET' | 'HEAD' | 'PATCH' | 'POST' | 'PUT' | 'DELETE' | 'CONNECT' | 'OPTIONS' | 'TRACE' | 'get' | 'head' | 'patch' | 'post' | 'put' | 'delete' | 'connect' | 'options' | 'trace',
    endpoint: string,
    options = {}
  ) => {
    const {
      body = null,
      params = {} as Record<string, any>,
      authType = AuthType.Customer, 
      customHeaders = {} as Record<string, string>,
      lazy = true, 
      server = true, 
    } = options as {
      body?: any
      params?: Record<string, any>
      authType?: AuthType
      customHeaders?: Record<string, string>
      lazy?: boolean
      server?: boolean
    }

    let token: string | null = null

    // Xử lý khác nhau cho client và server side
    if (import.meta.client) {
      // Client-side: lấy từ cookie browser
      if (authType === AuthType.Customer) {
        token = useCookie('customer_access_token', { maxAge: 7200 }).value || null
      }
      if (authType === AuthType.User) {
        token = useCookie('user_access_token', { maxAge: 7200 }).value || null
      }
    } else if (import.meta.server) {
      // Server-side: lấy từ request headers
      const cookieHeaders = useRequestHeaders(['cookie'])
      
      if (cookieHeaders.cookie) {
        const cookies = cookieHeaders.cookie.split(';').map(c => c.trim())
        
        if (authType === AuthType.Customer) {
          const customerCookie = cookies.find(c => c.startsWith('customer_access_token='))
          if (customerCookie) {
            token = customerCookie.split('=')[1]
          }
        }
        
        if (authType === AuthType.User) {
          const userCookie = cookies.find(c => c.startsWith('user_access_token='))
          if (userCookie) {
            token = userCookie.split('=')[1]
          }
        }
      }
    }

    const headers: Record<string, string> = {
      ...(body instanceof FormData ? {} : { 'Content-Type': 'application/json' }), 
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
    patch: <T>(endpoint: string, body: any, options = {}) => {
      // Chuyển PATCH thành POST với _method=PATCH
      const updatedBody = body instanceof FormData
        ? body // Nếu là FormData, thêm trực tiếp vào FormData
        : { ...body, _method: 'PATCH' } // Nếu là object, thêm _method vào body

      if (body instanceof FormData) {
        updatedBody.append('_method', 'PATCH')
      }

      return apiFetch<T>('POST', endpoint, { body: updatedBody, ...options })
    },
    del: <T>(endpoint: string, options = {}) => apiFetch<T>('DELETE', endpoint, options),
  }
}