import { useCustomerAuth } from '~/composables/useCustomerAuth'

export default defineNuxtRouteMiddleware(async (to, from) => {
  // Skip middleware for admin routes
  if (to.path.startsWith('/admin')) {
    return
  }

  const { isAuthenticated, accessToken, refreshToken, refreshAccessToken, fetchCurrentCustomer } = useCustomerAuth()

  // Nếu có refreshToken và đang vào /login, chuyển về trang chính
  if (to.path === '/login' && refreshToken.value) {
    return navigateTo('/')
  }

  // Handle account and order protected routes
  if (to.path.startsWith('/account') || to.path.startsWith('/order')) {
    // Nếu không có token nào và không ở trang login, chuyển về login
    if (!accessToken.value && !refreshToken.value) {
      if (to.path !== '/login') {
        return navigateTo('/login')
      }
      return
    }

    // Nếu không authenticated sau khi thử làm mới token, chuyển về login
    if (!refreshToken.value && to.path !== '/login') {
      return navigateTo('/login')
    }
  }

  // Nếu có refreshToken nhưng accessToken hết hạn, làm mới token
  if (!accessToken.value && refreshToken.value) {
    const refreshed = await refreshAccessToken()
    await fetchCurrentCustomer() // Lấy lại thông tin user sau khi làm mới
    if (!refreshed) {
      // Nếu refresh thất bại và không ở trang login, chuyển về login
      if (to.path !== '/login') {
        return navigateTo('/login')
      }
      return
    }
  }

  // Continue navigation
})