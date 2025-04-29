import { useApi, AuthType } from './useApi';
import type { Parameter } from '~/types/parameter';

export const useParameters = () => {
    const { get, put } = useApi();

    const getParameters = async (names: string[], authType: AuthType = AuthType.Guest) => {
        return await get<Parameter>('/parameters', {
            authType,
            params: { name: names.join(' ') },
            cache: false
        });
    };

    const updateParameters = async (
        name: string,
        value: string,
        authType: AuthType = AuthType.User
    ) => {
        return await put('/parameters', { name, value }, {
            authType,
        });
    };

    const getShippingFee = async (authType: AuthType = AuthType.Guest) => {
        return await get<Parameter>('/parameters', {
            authType,
            params: { name: 'shipping_fee' },
        });
    };

    const updateShippingFee = async (
        shippingFeeData: string,
        authType: AuthType = AuthType.User
    ) => {
        return await put('/parameters', { name: 'shipping_fee', value: shippingFeeData }, {
            authType,
        });
    };

    const getPhoneContact = async (authType: AuthType = AuthType.Guest) => {
        return await get<Parameter>('/parameters', {
            authType,
            params: { name: 'phone_contact' },
        });
    };

    const updatePhoneContact = async (
        phoneContactData: string,
        authType: AuthType = AuthType.User
    ) => {
        return await put('/parameters', { name: 'phone_contact', value: phoneContactData }, {
            authType,
        });
    };

    const getSocialLinks = async (names: string[], authType: AuthType = AuthType.Guest) => {
        return await get<Parameter>('/parameters', {
            authType,
            params: { name: names.join(' ') },
        });
    };

    const updateSocialLink = async (
        linkName: string,
        linkValue: string,
        authType: AuthType = AuthType.User
    ) => {
        return await put('/parameters', { name: linkName, value: linkValue }, {
            authType,
        });
    };

    return {
        getShippingFee,
        updateShippingFee,
        getPhoneContact,
        updatePhoneContact,
        getSocialLinks,
        updateSocialLink,
        getParameters,
        updateParameters,
    };
};
