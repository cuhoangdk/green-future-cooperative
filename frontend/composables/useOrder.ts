import { useApi, AuthType } from './useApi';
import type { Order } from '~/types/order';
export const useOrder = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách sản phẩm trong giỏ hàng
    const getOrders = async (page: number = 1,perPage: number = 10) => {
        return await get<Order>('/orders', {
            arams: { page, per_page: perPage },
            authType: AuthType.Customer,
        });
    };

    // Thêm sản phẩm vào giỏ hàng
    const addOrder = async (formData: FormData) => {
        return await post<Order>('/orders', formData, {
            authType: AuthType.Customer,
        });
    };

    // Lấy thông tin chi tiết sản phẩm trong giỏ hàng
    const getOrderById = async (id: number) => {
        return await get<Order>(`/orders/${id}`, {
            authType: AuthType.Customer,
        });
    };


    // Xóa sản phẩm khỏi giỏ hàng
    const cancelOrder = async (id: number) => {
        return await post(`/orders/${id}/cancel`, {
            authType: AuthType.Customer,
        });
    };

    return {
        getOrders,
        addOrder,
        getOrderById,
        cancelOrder,
    };
};
