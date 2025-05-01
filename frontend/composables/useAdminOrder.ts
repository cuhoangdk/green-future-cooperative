import { useApi, AuthType } from './useApi';
import type { Order, OrderHistory } from '~/types/order';

export const useAdminOrder = () => {
    const { get, post, put } = useApi();

    // Lấy danh sách đơn hàng
    const getAdminOrders = async (filters: {
        page?: number,
        per_page?: number,
        sort_by?: string,
        sort_direction?: string,
        search?: string,
        user_id?: number,
        category_id?: number,
        status?: string,
        start_date?: string,
        end_date?: string,
        is_hot?: number,
        is_featured?: number,
      }, authType: AuthType = AuthType.User) => {
        return await get<Order>('/admin/orders/search', {
            params: {  ...filters, sort_by: filters.sort_by ?? 'updated_at'  },
            authType
        });
    };

    const getOrderHistories = async (orderId: string, authType: AuthType = AuthType.User) => {
        return await get<OrderHistory>(`/order-histories/${orderId}`, {
            authType
        });
    };

    // Tạo đơn hàng mới
    const createAdminOrder = async (formData: FormData) => {
        return await post<Order>('/admin/orders', formData, {
            authType: AuthType.User,
        });
    };

    // Lấy thông tin chi tiết đơn hàng
    const getAdminOrderById = async (id: string) => {
        return await get<Order>(`/admin/orders/${id}`, {
            authType: AuthType.User,
        });
    };

    // Cập nhật đơn hàng
    const updateAdminOrder = async (id: string, formData: FormData) => {
        return await put<Order>(`/admin/orders/${id}`, formData, {
            authType: AuthType.User,
        });
    };

    // Hủy đơn hàng
    const cancelAdminOrder = async (id: string, cancelledReason: string) => {
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
        getOrderHistories
    };
};
