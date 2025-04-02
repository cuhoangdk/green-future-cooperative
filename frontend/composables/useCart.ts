import { useApi, AuthType } from './useApi';
import type { CartItem } from '~/types/cart';
export const useCart = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách sản phẩm trong giỏ hàng
    const getCartItems = async () => {
        return await get<CartItem>('/cart', {
            authType: AuthType.Customer,
        });
    };

    // Thêm sản phẩm vào giỏ hàng
    const addCartItem = async (productId: number, quantity: number) => {
        return await post<CartItem>('/cart', { product_id: productId, quantity }, {
            authType: AuthType.Customer,
        });
    };

    // Lấy thông tin chi tiết sản phẩm trong giỏ hàng
    const getCartItemById = async (id: number) => {
        return await get<CartItem>(`/cart/${id}`, {
            authType: AuthType.Customer,
        });
    };

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    const updateCartItem = async (id: number, quantity: number) => {
        return await put<CartItem>(`/cart/${id}`, { quantity }, {
            authType: AuthType.Customer,
        });
    };

    // Xóa sản phẩm khỏi giỏ hàng
    const deleteCartItem = async (id: number) => {
        return await del(`/cart/${id}`, {
            authType: AuthType.Customer,
        });
    };

    return {
        getCartItems,
        addCartItem,
        getCartItemById,
        updateCartItem,
        deleteCartItem,
    };
};
