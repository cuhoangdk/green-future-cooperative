import { useApi, AuthType } from './useApi';
import type { Log } from '~/types/log';

export const useActivityLogs = () => {
  const { get } = useApi();

  // Lấy danh sách activity logs (có thể phân trang)
  const getActivityLogs = async (
    page: number = 1,
    perPage: number = 10,
    sortBy: string = 'created_at',
    sortDirection: 'asc' | 'desc' = 'desc',
    authType: AuthType = AuthType.User
  ) => {
    return await get<Log[]>('/activity-logs', {
      params: { page, per_page: perPage, sort_by: sortBy, sort_direction: sortDirection },
      authType,
    });
  };

  // Tìm kiếm activity logs theo tiêu chí
  const searchActivityLogs = async (
    filters: {
      page?: number;
      per_page?: number;
      sort_by?: string;
      sort_direction?: string;
      search?: string;
    },
    authType: AuthType = AuthType.User
  ) => {
    return await get<Log[]>('/activity-logs/search', {
      params: { ...filters },
      authType,
    });
  };

  // Lấy chi tiết một activity log theo ID
  const getActivityLogById = async (id: number, authType: AuthType = AuthType.User) => {
    return await get<Log>(`/activity-logs/${id}`, {
      authType,
    });
  };

  return {
    getActivityLogs,
    searchActivityLogs,
    getActivityLogById,
  };
};
