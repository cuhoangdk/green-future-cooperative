import { useApi, AuthType } from './useApi';
import type { Customer, CustomerAddress } from '~/types/customer';

export const useCustomer = () => {
    const { get, post, put, del, patch } = useApi();

    // Lấy danh sách khách hàng (có thể phân trang)
    const getCustomers = async (
        page: number = 1,
        perPage: number = 100,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Customer[]>('/customers', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Tìm kiếm khách hàng theo tiêu chí cơ bản
    const searchCustomers = async (
        filters: {
            page?: number;
            per_page?: number;
            search?: string;
        },
        authType: AuthType = AuthType.User
    ) => {
        return await get<Customer[]>('/customers/search', {
            params: { ...filters },
            authType,
        });
    };

    // Lấy danh sách khách hàng đã bị xóa mềm (trashed)
    const getTrashedCustomers = async (
        page: number = 1,
        perPage: number = 10,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Customer[]>('/customers/trashed', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy thông tin một khách hàng theo id
    const getCustomerById = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Customer>(`/customers/${id}`, {
            authType,
        });
    };

    // Tạo khách hàng mới
    const createCustomer = async (
        customerData: FormData | Partial<Customer>,
        authType: AuthType = AuthType.User
    ) => {
        return await post<Customer>('/customers/', customerData, {
            authType,
        });
    };

    // Cập nhật thông tin khách hàng
    const updateCustomer = async (
        id: number,
        customerData: FormData | Partial<Customer>,
        authType: AuthType = AuthType.User
    ) => {
        return await put<Customer>(`/customers/${id}`, customerData, {
            authType,
        });
    };

    // Xóa mềm khách hàng
    const deleteCustomer = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/customers/${id}`, {
            authType,
        });
    };

    // Khôi phục khách hàng từ thùng rác
    const restoreCustomer = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await patch<Customer>(`/customers/restore/${id}`, null, {
            authType,
        });
    };

    // Xóa vĩnh viễn khách hàng
    const forceDeleteCustomer = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/customers/force-delete/${id}`, {
            authType,
        });
    };

    // Đổi mật khẩu khách hàng
    const changeCustomerPassword = async (
        id: number,
        data: {
            password: string;
            password_confirmation: string;
        },
        authType: AuthType = AuthType.User
    ) => {
        return await put<Customer>(`/customers/change-password/${id}`, data, {
            authType,
        });
    };

    // Lấy danh sách địa chỉ của khách hàng
    const getCustomerAddresses = async (
        customerId: number,
        authType: AuthType = AuthType.User
    ) => {
        return await get<CustomerAddress[]>(`/customers/${customerId}/addresses`, {
            authType,
        });
    };

    // Thêm địa chỉ mới cho khách hàng
    const createCustomerAddress = async (
        customerId: number,
        addressData: FormData | Partial<CustomerAddress>,
        authType: AuthType = AuthType.User
    ) => {
        return await post<CustomerAddress>(`/customers/${customerId}/addresses`, addressData, {
            authType,
        });
    };

    // Cập nhật địa chỉ của khách hàng
    const updateCustomerAddress = async (
        customerId: number,
        addressId: number,
        addressData: FormData | Partial<CustomerAddress>,
        authType: AuthType = AuthType.User
    ) => {
        return await put<CustomerAddress>(`/customers/${customerId}/addresses/${addressId}`, addressData, {
            authType,
        });
    };

    // Xóa địa chỉ của khách hàng
    const deleteCustomerAddress = async (
        customerId: number,
        addressId: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/customers/${customerId}/addresses/${addressId}`, {
            authType,
        });
    };

    return {
        getCustomers,
        searchCustomers,
        getTrashedCustomers,
        getCustomerById,
        createCustomer,
        updateCustomer,
        deleteCustomer,
        restoreCustomer,
        forceDeleteCustomer,
        changeCustomerPassword,
        getCustomerAddresses,
        createCustomerAddress,
        updateCustomerAddress,
        deleteCustomerAddress,
    };
}; 