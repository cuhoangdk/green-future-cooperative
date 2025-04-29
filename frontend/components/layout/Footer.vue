<template>
  <div class="pb-[80px] lg:pb-4 bg-[#1b1a1a] text-white p-4 pt-12">
    <div class="text-left max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <div class="flex justify-center items-center">
        <img src="~/assets/images/logo.png" alt="Logo Green Future" class="w-3/5 rounded-full object-cover" />
      </div>

      <div class="col-span-2 lg:col-span-1">
        <h3 class="text-xl font-semibold text-white mb-4">Quick links</h3>
        <ul class="grid grid-cols-2">
          <li v-for="link in quickLinks" :key="link.path">
            <NuxtLink :to="link.path" class="text-gray-300 hover:text-white transition-colors duration-200">
              {{ link.text }}
            </NuxtLink>
          </li>
        </ul>
      </div>

      <div class="col-span-2 lg:col-span-1">
        <h3 class="text-xl font-semibold text-white mb-4">Thông tin liên hệ</h3>
        <address class="text-gray-300 not-italic">
          <p>Địa chỉ: {{ parameters.address }}</p>
          <p>Điện thoại: <a :href="'tel:' + parameters.phone_contact" class="hover:text-white">{{
            parameters.phone_contact }}</a></p>
          <p>Email: <a :href="'mailto:' + parameters.email_contact" class="hover:text-white">{{
            parameters.email_contact }}</a></p>
        </address>
      </div>

      <div>
        <h3 class="text-xl font-semibold text-white mb-4">Theo dõi chúng tôi</h3>
        <div class="flex space-x-4">
          <a :href="parameters.facebook" target="_blank"
            class="text-gray-300 hover:text-white transition-colors duration-200">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                clip-rule="evenodd" />
            </svg>
          </a>
          <a :href="parameters.instagram" target="_blank"
            class="text-gray-300 hover:text-white transition-colors duration-200">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M12.315 2c2.43 0 2.784.013 3.808.06 1.02.049 1.975.232 2.688.553.733.33 1.338.762 1.94 1.364.602.602 1.033 1.207 1.364 1.94.32.713.503 1.668.552 2.688.047 1.024.06 1.378.06 3.808s-.013 2.784-.06 3.808c-.049 1.02-.232 1.975-.553 2.688-.33.733-.762 1.338-1.364 1.94-.602.602-1.207 1.033-1.94 1.364-.713.32-1.668.503-2.688.552-1.024.047-1.378.06-3.808.06s-2.784-.013-3.808-.06c-1.02-.049-1.975-.232-2.688-.553-.733-.33-1.338-.762-1.94-1.364-.602-.602-1.033-1.207-1.364-1.94-.32-.713-.503-1.668-.552-2.688-.047-1.024-.06-1.378-.06-3.808s.013-2.784.06-3.808c.049-1.02.232-1.975.553-2.688.33-.733.762-1.338 1.364-1.94.602-.602 1.207-1.033 1.94-1.364.713-.32 1.668-.503 2.688-.552 1.024-.047 1.378-.06 3.808-.06zm0 1.441c-2.39 0-2.693.013-3.645.057-.95.045-1.597.216-2.167.46-.586.25-1.083.583-1.577 1.077-.494.494-.827.991-1.077 1.577-.244.57-.415 1.217-.46 2.167-.044.952-.057 1.255-.057 3.645s.013 2.693.057 3.645c.045.95.216 1.597.46 2.167.25.586.583 1.083 1.077 1.577.494.494.991.827 1.577 1.077.57.244 1.217.415 2.167.46.952.044 1.255.057 3.645.057s2.693-.013 3.645-.057c.95-.045 1.597-.216 2.167-.46.586-.25 1.083-.583 1.577-1.077.494-.494.827-.991 1.077-1.577.244-.57.415-1.217.46-2.167.044-.952.057-1.255.057-3.645s-.013-2.693-.057-3.645c-.045-.95-.216-1.597-.46-2.167-.25-.586-.583-1.083-1.077-1.577-.494-.494-.991-.827-1.577-1.077-.57-.244-1.217-.415-2.167-.46-.952-.044-1.255-.057-3.645-.057zm0 4.138a5.676 5.676 0 100 11.352 5.676 5.676 0 000-11.352zm0 9.377a3.701 3.701 0 110-7.402 3.701 3.701 0 010 7.402zm7.224-9.377a1.326 1.326 0 11-2.652 0 1.326 1.326 0 012.652 0z"
                clip-rule="evenodd" />
            </svg>
          </a>
          <a :href="parameters.tiktok" target="_blank"
            class="text-gray-300 hover:text-white transition-colors duration-200">
            <svg class="w-6 h-6uu" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.89 2.89 0 01.88.15V9.28a6.34 6.34 0 00-1.47-.15A6.34 6.34 0 003 15.62a6.34 6.34 0 0010.86 4.48 6.34 6.34 0 002.51-5.13V9.25a8.28 8.28 0 004.85 1.57V7.36a4.83 4.83 0 01-3.63-.94z" />
            </svg>
          </a>
        </div>
      </div>
    </div>
    <hr class="my-6 border-gray-600" />
    <p class="text-center text-gray-400">© 2025 H&H. All rights reserved.</p>
  </div>
</template>

<script setup lang="ts">
const quickLinks = [
  { path: '/', text: 'Trang chủ' },
  { path: '/products', text: 'Sản phẩm' },
  { path: '/posts', text: 'Bài viết' },
  { path: '/about', text: 'Giới thiệu' },
  { path: '/contact', text: 'Liên hệ' }
]

const { parameters } = useParametersStore()
</script>