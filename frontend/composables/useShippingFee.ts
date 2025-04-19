import { useApi, AuthType } from './useApi';
import type { Parameter } from '~/types/parameter';

export const useShippingFee = () => {
    const { get, put } = useApi();

    const getShippingFee = async (authType: AuthType = AuthType.Guest) => {
        return await get<Parameter>('/shipping-fee', {
            authType,
        });
    };

    const updateShippingFee = async (
        shippingFeeData: string,
        authType: AuthType = AuthType.User
    ) => {
        return await put('/shipping-fee', {value: shippingFeeData}, {
            authType,
        });
    };

    return {
        getShippingFee,
        updateShippingFee,
    };
};
