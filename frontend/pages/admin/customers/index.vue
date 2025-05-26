<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
      <div class="flex flex-col w-full md:flex-row gap-2">
        <div class="relative w-full md:w-auto">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
          <input v-model="searchQuery" type="search" placeholder="Tìm kiếm khách hàng..."
            class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
          <select v-model="isBanned" class="select select-sm select-primary w-full sm:w-[150px]" @change="search">
            <option value="">Tất cả trạng thái</option>
            <option value="0">Hoạt động</option>
            <option value="1">Bị cấm</option>
          </select>
          <select v-model="sortBy" class="select select-sm select-primary w-full sm:w-[150px]" @change="search">
            <option value="">Sắp xếp theo</option>
            <option value="created_at+desc">Mới nhất</option>
            <option value="created_at+asc">Cũ nhất</option>
            <option value="full_name">Theo tên</option>
            <option value="email">Theo email</option>
          </select>
        </div>
      </div>
      <button @click="exportToExcel" class="btn btn-sm btn-secondary w-full md:w-auto">
        Xuất Excel
      </button>
      <button @click="$router.push('/admin/customers/create')" class="btn btn-sm btn-primary w-full md:w-auto">
        <Plus class="w-5 h-5" /> Thêm
      </button>
    </div>

    <!-- Content Wrapper with Loading Overlay -->
    <div class="relative">
      <!-- Loading Overlay specific to table/grid -->
      <div v-if="status === 'pending'"
        class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
        <span class="loading loading-spinner loading-lg"></span>
      </div>

      <TableCustomer :customers="customers.customers" :on-toggle-status="handleToggleStatus"
        :on-delete="handleDeleteCustomer" />

      <GridCustomer :customers="customers.customers" :on-toggle-status="handleToggleStatus"
        :on-delete="handleDeleteCustomer" />

      <!-- <div v-else-if="customers.customers.length === 0" class="flex flex-col items-center justify-center p-8">
        <Box class="w-16 h-16 text-gray-300" />
        <p class="text-gray-500 mt-2">Không tìm thấy khách hàng</p>
      </div> -->
    </div>



    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 m-4">
      <div class="flex items-center space-x-2">
        <p class="text-sm text-gray-600">{{ customers.customers.length }} / {{ customers.meta?.total }}</p>
        <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
          <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
        </select>
      </div>
      <UiPagination :links="customers.links" :meta="customers.meta" :show-first-last="true" :show-numbers="true"
        @page-change="handlePageChange" />
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Khách hàng', description: 'Quản lý khách hàng trên trang web' })

import { Search, Plus, Box } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Customer } from '~/types/customer'
import * as XLSX from 'xlsx';

const { searchCustomers, deleteCustomer, updateCustomer } = useCustomer()
const { getFullAddressName } = useVietnamAddress()
const swal = useSwal()
const { $toast } = useNuxtApp()
const router = useRouter()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const isBanned = ref('')
const sortBy = ref('')
const debouncedSearch = debounce(search, 500)

const { data, status } = await searchCustomers({ page: currentPage.value, per_page: perPage.value })
const customers = computed<{ customers: Customer[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
  customers: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
  meta: data.value?.meta ?? null,
  links: data.value?.links ?? null,
}))

async function search() {
  const filters: any = {
    page: currentPage.value,
    per_page: perPage.value,
    ...(searchQuery.value && { search: searchQuery.value }),
    ...(isBanned.value && { is_banned: isBanned.value }),
    ...(sortBy.value && {
      sort_by: sortBy.value.includes('+') ? sortBy.value.split('+')[0] : sortBy.value,
      sort_direction: sortBy.value.endsWith('+desc') ? 'desc' : 'asc'
    }),
  }

  const { error } = await searchCustomers(filters)
  if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách khách hàng!', 'error')
}

const handlePageChange = (page: number) => {
  currentPage.value = page
  search()
}

async function handleDeleteCustomer(customerId: number) {
  const result = await swal.fire({
    title: 'Xác nhận xóa',
    text: 'Bạn có chắc muốn xóa khách hàng này không?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
  })

  if (result.isConfirmed) {
    try {
      const { error } = await deleteCustomer(customerId)
      if (error.value) throw new Error(error.value.message)
      customers.value.customers = customers.value.customers.filter((customer: Customer) => customer.id !== customerId)
      $toast.success('Khách hàng đã được xóa!')
    } catch (err) {
      $toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
    }
  }
}

async function handleToggleStatus(customer: Customer) {
  try {
    const newBanStatus = !customer.is_banned
    const { error } = await updateCustomer(customer.id, { is_banned: newBanStatus })
    if (error.value) throw new Error(error.value.message)

    customer.is_banned = newBanStatus
    $toast.success(`Đã ${newBanStatus ? 'khóa' : 'mở khóa'} tài khoản thành công!`)
  } catch (err) {
    $toast.error(`Thao tác thất bại: ${(err as Error).message || 'Unknown error'}`)
  }
}

function exportToExcel() {
  // Kiểm tra nếu không có khách hàng
  if (!customers.value.customers.length) {
    $toast.error('Không có khách hàng để xuất!');
    return;
  }

  // Chuẩn bị dữ liệu khách hàng
  const exportData = customers.value.customers.map(customer => ({
    'Mã khách hàng': customer.id,
    'Họ và tên': customer.full_name,
    'Email': customer.email || 'N/A',
    'Số điện thoại': customer.phone_number || 'N/A',
    'Trạng thái': customer.is_banned ? 'Bị cấm' : 'Hoạt động',
  }));

  // Tạo workbook và worksheet
  const workbook = XLSX.utils.book_new();
  const worksheet = XLSX.utils.json_to_sheet([]); // Tạo sheet rỗng

  // Thêm dòng tiêu đề
  XLSX.utils.sheet_add_aoa(worksheet, [['BÁO CÁO KHÁCH HÀNG']], { origin: 'A1' });
  XLSX.utils.sheet_add_aoa(worksheet, [[]], { origin: 'A2' }); // Dòng trống

  // Thêm header và dữ liệu khách hàng
  XLSX.utils.sheet_add_json(worksheet, exportData, { origin: 'A3', skipHeader: false });

  // Tùy chỉnh độ rộng cột
  worksheet['!cols'] = [
    { wch: 15 }, // Mã khách hàng
    { wch: 25 }, // Họ và tên
    { wch: 30 }, // Email
    { wch: 15 }, // Số điện thoại
    { wch: 15 }  // Trạng thái
  ];

  // Định dạng tiêu đề và header
  // Tiêu đề chính
  worksheet['A1'].s = {
    font: { bold: true, sz: 16 },
    alignment: { horizontal: 'center' }
  };

  // Định dạng header (dòng A3:E3)
  const headerRowIndex = 3;
  for (let col = 0; col < 5; col++) {
    const cellAddress = XLSX.utils.encode_cell({ r: headerRowIndex - 1, c: col });
    worksheet[cellAddress].s = {
      font: { bold: true },
      alignment: { horizontal: 'center' }
    };
  }

  // Gộp ô cho tiêu đề
  worksheet['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 4 } }];

  // Thêm sheet vào workbook
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Khách hàng');

  // Xuất file
  const fileName = `KhachHang_${new Date().toISOString().slice(0, 10)}.xlsx`;
  XLSX.writeFile(workbook, fileName);
}

</script>