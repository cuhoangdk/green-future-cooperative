import { useUserAuth } from '~/composables/useUserAuth'

export default defineNuxtRouteMiddleware(async (to, from) => {
  // Chỉ áp dụng middleware cho các route bắt đầu với /admin/
  if (!to.path.startsWith('/admin')) {
    return
  }

  const { isAuthenticated, currentUser, accessToken, refreshToken, refreshAccessToken, fetchCurrentUser } = useUserAuth()

  // Nếu đang ở trang login và đã đăng nhập, chuyển hướng đến dashboard
  if (to.path === '/admin/login' && refreshToken.value) {
    return navigateTo('/admin')
  }

  // Nếu không có accessToken và refreshToken, chuyển hướng về login
  if (!accessToken.value && !refreshToken.value) {
    if (to.path !== '/admin/login') {
      return navigateTo('/admin/login')
    }
    return
  }

  // Nếu có refreshToken nhưng accessToken hết hạn, làm mới token
  if (!accessToken.value && refreshToken.value) {
    const refreshed = await refreshAccessToken()
    if (refreshed) {
      await fetchCurrentUser() // Lấy lại thông tin user sau khi làm mới
    } else {
      if (to.path !== '/admin/login') {
        return navigateTo('/admin/login') // Nếu refresh thất bại, chuyển về login
      }
      return
    }
  }

  // Nếu có refreshToken nhưng accessToken hết hạn, làm mới token
  if (accessToken.value && !currentUser.value) {
    await fetchCurrentUser()
  }

  // Nếu vẫn không có accessToken sau khi thử làm mới, chuyển về login
  if (!isAuthenticated.value) {
    if (to.path !== '/admin/login') {
      return navigateTo('/admin/login')
    }
    return
  }

  // Nếu đã xác thực, tiếp tục cho phép truy cập
})