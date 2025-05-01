import { useApi, AuthType } from './useApi';
import type { Notification } from '~/types/notification';

export const useNotifications = () => {
    const { get, put, del } = useApi();
    const router = useRouter();

    // Lấy danh sách thông báo
    const getNotifications = async (page: number = 1, perPage: number = 10) => {
        return await get<Notification>('/notifications', {
            params: { page, per_page: perPage },
            authType: AuthType.User,
        });
    };

    // Đánh dấu tất cả thông báo là đã đọc
    const markAllAsRead = async () => {
        return await put('/notifications/read-all', null, {
            authType: AuthType.User,
        });
    };

    // Lấy thông báo theo ID
    const getNotificationById = async (id: number) => {
        return await get<Notification>(`/notifications/${id}`, {
            authType: AuthType.User,
        });
    };

    // Đánh dấu thông báo là đã đọc
    const markAsRead = async (id: number) => {
        return await put(`/notifications/${id}/read`, null, {
            authType: AuthType.User,
        });
    };

    // Xóa thông báo
    const deleteNotification = async (id: number) => {
        return await del(`/notifications/${id}`, {
            authType: AuthType.User,
        });
    };

    const handleNotificationRedirect = (message: string) => {
        const orderMatch = message.match(/#ORD\d+/);
        if (orderMatch) {
          const orderId = orderMatch[0].replace('#', '');
          router.push(`/admin/orders/${orderId}`);
          return;
        }
    
        // Nếu không có #ORD, tìm mã sản phẩm
        const productMatch = message.match(/[A-Z0-9]+_[A-Z0-9]+_[A-Z0-9]\d+/);
        if (productMatch) {
          const productCode = productMatch[0];
          router.push(`/admin/products/${productCode}`);
          return;
        }
    }

    return {
        getNotifications,
        markAllAsRead,
        getNotificationById,
        markAsRead,
        deleteNotification,
        handleNotificationRedirect,
    };
};
