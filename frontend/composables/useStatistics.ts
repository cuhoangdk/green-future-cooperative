import { useApi, AuthType } from './useApi';
import type { Statistics } from '~/types/statistics';

export const useStatistics = () => {
    const { get } = useApi();

    // Lấy thống kê
    const getStatistics = async (
        startDate: string,
        endDate: string,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Statistics>('/statistics', {
            params: { start_date: startDate, end_date: endDate },
            authType,
        });
    };

    return {
        getStatistics,
    };
};
