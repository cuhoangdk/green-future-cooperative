import { useApi, AuthType } from './useApi';
import type { Farm } from '~/types/farm'; // Giả sử bạn có type Farm

export const useFarms = () => {
  const { get, post, put, del } = useApi();

  // Lấy danh sách nông trại (có thể phân trang)
  const getFarms = async (
    page: number = 1,
    perPage: number = 10,
    authType: AuthType = AuthType.Guest
  ) => {
    return await get<Farm>('/farms', {
      params: { page, per_page: perPage },
      authType,
    });
  };

  // Tìm kiếm nông trại theo tiêu chí
  const searchFarms = async (filters: {
    page?: number;
    per_page?: number;
    search?: string;
    status?: string;
    user_id?: number;
    start_date?: string;
    end_date?: string;
  }, authType: AuthType = AuthType.User) => {
    return await get<Farm>('/farms/search', {
      params: { ...filters },
      authType,
    });
  };

  // Lấy danh sách nông trại đã bị xóa mềm (trashed)
  const getTrashedFarms = async (
    page: number = 1,
    perPage: number = 10,
    authType: AuthType = AuthType.User
  ) => {
    return await get<Farm>('/farms/trashed', {
      params: { page, per_page: perPage },
      authType,
    });
  };

  // Lấy thông tin một nông trại theo ID
  const getFarmById = async (farmId: number, authType: AuthType = AuthType.Guest) => {
    return await get<Farm>(`/farms/${farmId}`, {
      authType,
    });
  };

  // Tạo nông trại mới
  const createFarm = async (farmData: FormData) => {
    return await post<Farm>('/farms', farmData, {
      authType: AuthType.User,
    });
  };

  // Cập nhật thông tin nông trại
  const updateFarm = async (farmId: number, farmData: FormData) => {
    return await put<Farm>(`/farms/${farmId}`, farmData, {
      authType: AuthType.User,
    });
  };

  // Xóa mềm nông trại
  const deleteFarm = async (farmId: number) => {
    return await del(`/farms/${farmId}`, {
      authType: AuthType.User,
    });
  };

//   // Khôi phục nông trại từ thùng rác
//   const restoreFarm = async (farmId: number) => {
//     return await patch(`/farms/restore/${farmId}`, null, {
//       authType: AuthType.User,
//     });
//   };

  // Xóa vĩnh viễn nông trại
  const forceDeleteFarm = async (farmId: string) => {
    return await del(`/farms/force-delete/${farmId}`, {
      authType: AuthType.User,
    });
  };

  return {
    getFarms,
    searchFarms,
    getTrashedFarms,
    getFarmById,
    createFarm,
    updateFarm,
    deleteFarm,
    // restoreFarm,
    forceDeleteFarm,
  };
};