interface AddressItem {
  id: string;
  name: string;
  name_en: string;
  full_name: string;
  full_name_en: string;
  latitude: string;
  longitude: string;
}

interface AddressDetails extends AddressItem {
  phuong: string;
  quan: string;
  tinh: string;
}

interface ApiResponse {
  error: number;
  error_text: string;
  data_name: string;
  data: AddressItem[] ;
}

interface ApiResponseDetails {
  error: number;
  error_text: string;
  data_name: string;
  data: AddressDetails;
}

// Base URL của API
const BASE_URL = 'https://esgoo.net/api-tinhthanh';

// Hàm fetch dữ liệu chung với useAsyncData
const fetchAddressData = async (type: number, id: string = '0') => {
  const url = `${BASE_URL}/${type}/${id}.htm`;
  const { data, error } = await useAsyncData<ApiResponse>(`address-${type}-${id}`, () =>
    $fetch(url)
  );

  if (error.value || data.value?.error !== 0) {
    throw new Error(error.value?.message || data.value?.error_text || 'Failed to fetch address data');
  }

  return data.value.data;
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

  // Lấy tên đầy đủ của địa điểm theo ID phường/xã
  const getFullAddressName = async (wardId: string) => {
    try {
      if (!wardId) return '';

      const { data, error } = await useAsyncData<ApiResponseDetails>(`full-address-${wardId}`, () =>
        $fetch(`https://esgoo.net/api-tinhthanh/5/${wardId}.htm`)
      );

      if (error.value || data.value?.error !== 0) {
        throw new Error(error.value?.message || data.value?.error_text || 'Failed to fetch address name');
      }

      return data.value.data.full_name;
    } catch (error) {
      console.error('Error fetching address name:', error);
      return '';
    }
  };

  return {
    provinces,
    districts,
    wards,
    fetchProvinces,
    fetchDistricts,
    fetchWards,
    getFullAddressName,
  };
};