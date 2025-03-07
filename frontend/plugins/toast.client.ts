import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css' // Import CSS mặc định
import { defineNuxtPlugin } from '#app'

export default defineNuxtPlugin((nuxtApp): { provide: { toast: typeof Toast } } => {
  nuxtApp.vueApp.use(Toast, {
    position: 'top-right', // Vị trí toast
    timeout: 3000,        // Thời gian hiển thị (ms)
    closeOnClick: true,   // Đóng khi click
    pauseOnHover: true,   // Tạm dừng khi hover
    draggable: true,      // Có thể kéo
    draggablePercent: 0.6,// Phần trăm kéo để đóng
    showCloseButtonOnHover: false, // Hiển thị nút đóng khi hover
    hideProgressBar: false,       // Ẩn thanh tiến trình
    closeButton: 'button',        // Loại nút đóng
    icon: true,                   // Hiển thị icon
    rtl: false,                  // Không phải right-to-left
  })

  // Trả về để có thể inject nếu cần
  return {
    provide: {
      toast: nuxtApp.vueApp.config.globalProperties.$toast
    }
  }
})