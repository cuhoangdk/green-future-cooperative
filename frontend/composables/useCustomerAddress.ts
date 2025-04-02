import { useApi, AuthType } from './useApi';
import type { CustomerAddress } from '~/types/customer';
export const useCustomerAddress = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách sản phẩm trong giỏ hàng
    const getCustomerAddress = async () => {
        return await get<CustomerAddress>('/customer-profile/addresses/', {
            authType: AuthType.Customer,
        });
    };

    // Thêm sản phẩm vào giỏ hàng
    const addCustomerAddress = async (formData: FormData) => {
        return await post<CustomerAddress>('/customer-profile/addresses/', formData, {
            authType: AuthType.Customer,
        });
    };

    // Lấy thông tin chi tiết sản phẩm trong giỏ hàng
    const updateCustomerAddress = async (id: number) => {
        return await put<CustomerAddress>(`/customer-profile/addresses/${id}`, {
            authType: AuthType.Customer,
        });
    };

    // Xóa sản phẩm khỏi giỏ hàng
    const deleteCustomerAddress = async (id: number) => {
        return await del(`/customer-profile/addresses/${id}`, {
            authType: AuthType.Customer,
        });
    };

    return {
        getCustomerAddress,
        addCustomerAddress,
        updateCustomerAddress,
        deleteCustomerAddress,
    };
};
