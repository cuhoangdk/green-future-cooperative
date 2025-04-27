import { useApi, AuthType } from './useApi';
import type { ContactMessage } from '~/types/contact-message';

export const useContactMessages = () => {
    const { get, post, put, del } = useApi();

    const createMessage = async (formData: FormData) => {
        return await post<ContactMessage>('/contact-messages', formData, {
            authType: AuthType.Guest,
        });
    };

    const getMessages = async (page: number = 1, perPage: number = 10) => {
        return await get<ContactMessage>('/contact-messages', {
            params: { page, per_page: perPage },
            authType: AuthType.User,
        });
    };

    const getMessageById = async (id: number) => {
        return await get<ContactMessage>(`/contact-messages/${id}`, {
            authType: AuthType.User,
        });
    };

    const updateMessage = async (id: number, formData: FormData) => {
        return await put<ContactMessage>(`/contact-messages/${id}`, formData, {
            authType: AuthType.User,
        });
    };

    const deleteMessage = async (id: number) => {
        return await del(`/contact-messages/${id}`, {
            authType: AuthType.User,
        });
    };

    return {
        createMessage,
        getMessages,
        getMessageById,
        updateMessage,
        deleteMessage,
    };
};
