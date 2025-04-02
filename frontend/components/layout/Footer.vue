<template>
    <div class="bg-[#333031] text-white p-4 pt-12">
      <div class="text-left max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="col-span-2">
          <h2 class="text-3xl font-bold text-green-500 mb-4">Green Future</h2>
          <p class="text-gray-300">
            Hợp tác xã Green Future là đơn vị tiên phong trong việc cung cấp thực phẩm sạch và an toàn...
          </p>
        </div>
  
        <div class="col-span-2 lg:col-span-1">
          <h3 class="text-xl font-semibold text-white mb-4">Quick links</h3>
          <ul class="space-y-2">
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
            <p>Địa chỉ: Buôn Hồ, Đắk Lắk, Việt Nam</p>
            <p>Điện thoại: <a href="tel:0819530009" class="hover:text-white">0819530009</a></p>
            <p>Email: <a href="mailto:nhokkho18@gmail.com" class="hover:text-white">nhokkho18@gmail.com</a></p>
          </address>
        </div>
      </div>
  
      <hr class="my-6 border-gray-600" />
      <p class="text-center text-gray-400">&copy; 2025 Nguyễn Đình Hiệu. All rights reserved.</p>
    </div>
  </template>
  
  <script setup lang="ts">
  const quickLinks = [
    { path: '/', text: 'Trang chủ' },
    { path: '/products', text: 'Sản phẩm' },
    { path: '/contact', text: 'Liên hệ' }
  ]
  </script>
  