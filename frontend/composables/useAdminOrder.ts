import { useApi, AuthType } from './useApi';
import type { Order } from '~/types/order';

export const useAdminOrder = () => {
    const { get, post, put } = useApi();

    // Lấy danh sách đơn hàng
    const getAdminOrders = async (page: number = 1,perPage: number = 10) => {
        return await get<Order>('/admin/orders', {
            params: { page, per_page: perPage },
            authType: AuthType.User,
        });
    };

    // Tạo đơn hàng mới
    const createAdminOrder = async (formData: FormData) => {
        return await post<Order>('/admin/orders', formData, {
            authType: AuthType.User,
        });
    };

    // Lấy thông tin chi tiết đơn hàng
    const getAdminOrderById = async (id: number) => {
        return await get<Order>(`/admin/orders/${id}`, {
            authType: AuthType.User,
        });
    };

    // Cập nhật đơn hàng
    const updateAdminOrder = async (id: number, formData: FormData) => {
        return await put<Order>(`/admin/orders/${id}`, formData, {
            authType: AuthType.User,
        });
    };

    // Hủy đơn hàng
    const cancelAdminOrder = async (id: number, cancelledReason: string) => {
        return await post(`/admin/orders/${id}/cancel`, { cancelled_reason: cancelledReason }, {
            authType: AuthType.User,
        });
    };

    return {
        getAdminOrders,
        createAdminOrder,
        getAdminOrderById,
        updateAdminOrder,
        cancelAdminOrder,
    };
};
