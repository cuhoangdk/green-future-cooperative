import { useApi, AuthType } from './useApi';
import type { CustomerAddress } from '~/types/customer';
export const useCustomerAddress = () => {
    const { get, post, put, del } = useApi();

    const getCustomerAddress = async () => {
        return await get<CustomerAddress>('/customer-profile/addresses', {
            authType: AuthType.Customer,
        });
    };

    const addCustomerAddress = async (formData: FormData) => {
        return await post<CustomerAddress>('/customer-profile/addresses', formData, {
            authType: AuthType.Customer,
        });
    };

    const updateCustomerAddress = async (id: number) => {
        return await put<CustomerAddress>(`/customer-profile/addresses/${id}`, {
            authType: AuthType.Customer,
        });
    };

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
