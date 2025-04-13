import { useApi, AuthType } from './useApi';
import type { CarouselItem } from '~/types/carousel';

export const useCarousel = () => {
    const { get, post, put, del } = useApi();

    const getAllSlide = async (authType: AuthType = AuthType.Guest) => {
        return await get<CarouselItem>('/slider-images', {
            authType,
        });
    };

    const getSlideById = async (id: number) => {
        return await get<CarouselItem>(`/slider-images/${id}`);
    };

    const createSlide = async (formData: FormData) => {
        return await post('/slider-images', formData, {
            authType: AuthType.User,
        });
    };

    const updateSlide = async (id: number, formData: FormData) => {
        return await put(`/slider-images/${id}`, formData, {
            authType: AuthType.User,
        });
    };

    const deleteSlide = async (id: number) => {
        return await del(`/slider-images/${id}`, {
            authType: AuthType.User,
        });
    };

    return {
        getAllSlide,
        getSlideById,
        createSlide,
        updateSlide,
        deleteSlide,
    };
};
