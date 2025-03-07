import Swal from 'sweetalert2'

export function useSwal() {
  // Tùy chỉnh các tùy chọn mặc định (nếu cần)
  const swal = Swal.mixin({
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Đồng ý',
    cancelButtonText: 'Hủy bỏ',
  })

  return swal
}