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

  // Lưu route đích mà người dùng muốn đến (dùng cookie hoặc state)
  const intendedRoute = useCookie('intended_route')

  // Handle account protected routes
  if (to.path.startsWith('/account')) {
    // Nếu không có token nào và không ở trang login, lưu route đích và chuyển về login
    if (!accessToken.value && !refreshToken.value) {
      if (to.path !== '/login') {
        intendedRoute.value = to.fullPath // Lưu toàn bộ path (bao gồm query params nếu có)
        return navigateTo('/login')
      }
      return
    }

    // Nếu không authenticated sau khi thử làm mới token, lưu route đích và chuyển về login
    if (!isAuthenticated.value && to.path !== '/login') {
      intendedRoute.value = to.fullPath
      return navigateTo('/login')
    }
  }

  // Nếu có refreshToken nhưng accessToken hết hạn, làm mới token
  if (!accessToken.value && refreshToken.value) {
    const refreshed = await refreshAccessToken()
    await fetchCurrentCustomer() // Lấy lại thông tin user sau khi làm mới
    if (refreshed) {
      // Nếu có intendedRoute (tức là bị chặn trước đó), chuyển về route đích
      if (intendedRoute.value && intendedRoute.value !== '/login') {
        const redirectTo = intendedRoute.value
        intendedRoute.value = null // Xóa intendedRoute sau khi dùng
        return navigateTo(redirectTo)
      }
    } else {
      // Nếu refresh thất bại và không ở trang login, lưu route đích và chuyển về login
      if (to.path !== '/login') {
        intendedRoute.value = to.fullPath
        return navigateTo('/login')
      }
      return
    }
  }

  // Continue navigation
})