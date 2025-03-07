import { ref } from 'vue';
import { useAsyncData } from '#app';

// Định nghĩa interface cho dữ liệu trả về từ API
interface AddressItem {
  id: string;
  name: string;
  name_en: string;
  full_name: string;
  full_name_en: string;
  latitude: string;
  longitude: string;
}

interface ApiResponse {
  error: number;
  error_text: string;
  data_name: string;
  data: AddressItem[];
}

// Base URL của API
const BASE_URL = 'https://esgoo.net/api-tinhthanh';

// Hàm fetch dữ liệu chung
const fetchAddressData = async (type: number, id: string = '0') => {
  const url = `${BASE_URL}/${type}/${id}.htm`;
  const response = await fetch(url);
  const data: ApiResponse = await response.json();

  if (!response.ok || data.error !== 0) {
    throw new Error(data.error_text || 'Failed to fetch address data');
  }

  return data.data;
};

// Composable chính
export const useVietnamAddress = () => {
  const provinces = ref<AddressItem[]>([]);
  const districts = ref<AddressItem[]>([]);
  const wards = ref<AddressItem[]>([]);

  // Lấy danh sách tỉnh/thành phố
  const fetchProvinces = async () => {
    try {
      provinces.value = await fetchAddressData(1, '0');
    } catch (error) {
      console.error('Error fetching provinces:', error);
    }
  };

  // Lấy danh sách quận/huyện theo tỉnh
  const fetchDistricts = async (provinceId: string) => {
    try {
      if (!provinceId) {
        districts.value = [];
        wards.value = [];
        return;
      }
      districts.value = await fetchAddressData(2, provinceId);
      wards.value = []; // Reset wards khi chọn tỉnh mới
    } catch (error) {
      console.error('Error fetching districts:', error);
    }
  };

  // Lấy danh sách phường/xã theo quận/huyện
  const fetchWards = async (districtId: string) => {
    try {
      if (!districtId) {
        wards.value = [];
        return;
      }
      wards.value = await fetchAddressData(3, districtId);
    } catch (error) {
      console.error('Error fetching wards:', error);
    }
  };

  return {
    provinces,
    districts,
    wards,
    fetchProvinces,
    fetchDistricts,
    fetchWards,
  };
};