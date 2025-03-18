import { useApi, AuthType } from './useApi';
import type { CultivationLog } from '~/types/product'; // Giả sử bạn có type Farm

export const useCultivationLogs = () => {
  const { get, post, put, del } = useApi();

  const getLogs = async (
    productId: number,
    page: number = 1,
    perPage: number = 10,
    sortBy: string = 'created_at',
    sortDir: 'asc' | 'desc' = 'asc',
    authType: AuthType = AuthType.Guest
  ) => {
    return await get<CultivationLog>(`/products/${productId}/cultivation-logs`, {
      params: { page, per_page: perPage, sort_by: sortBy, sort_direction: sortDir },
      authType,
    });
  };

  const getLogById = async (productId: number, logId: number, authType: AuthType = AuthType.Guest) => {
    return await get<CultivationLog>(`/products/${productId}/cultivation-logs/${logId}`, {
      authType,
    });
  };

  const createLog = async (productId: number ,logData: FormData) => {
    return await post<CultivationLog>(`/products/${productId}/cultivation-logs/`, logData, {
      authType: AuthType.User,
    });
  };

  const updateLog = async (productId: number, logId: number, logData: FormData) => {
    return await put<CultivationLog>(`/products/${productId}/cultivation-logs/${logId}`, logData, {
      authType: AuthType.User,
    });
  };

  const deleteLog = async (productId: number, logId: number) => {
    return await del(`/products/${productId}/cultivation-logs/${logId}`, {
      authType: AuthType.User,
    });
  };

  return {
    getLogs,
    getLogById,
    createLog,
    updateLog,
    deleteLog,
  };
};