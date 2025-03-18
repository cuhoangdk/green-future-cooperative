import { useApi, AuthType } from './useApi';
import type { ProductImage } from '~/types/product'; // Giả sử bạn có type Farm

export const useProductImages = () => {
  const { get, post, put, del } = useApi();

  const getImages = async (
    productId: number,
    page: number = 1,
    perPage: number = 10,
    sortBy: string = 'created_at',
    sortDir: 'asc' | 'desc' = 'asc',
    authType: AuthType = AuthType.Guest
  ) => {
    return await get<ProductImage>(`/products/${productId}/images`, {
      params: { page, per_page: perPage, sort_by: sortBy, sort_direction: sortDir },
      authType,
    });
  };

  const getImageById = async (productId: number, imgId: number, authType: AuthType = AuthType.Guest) => {
    return await get<ProductImage>(`/products/${productId}/images/${imgId}`, {
      authType,
    });
  };

  const createImage = async (productId: number ,logData: FormData) => {
    return await post<ProductImage>(`/products/${productId}/images/`, logData, {
      authType: AuthType.User,
    });
  };

  const updateImage = async (productId: number, imgId: number, imgData: FormData) => {
    return await put<ProductImage>(`/products/${productId}/images/${imgId}`, imgData, {
      authType: AuthType.User,
    });
  };

  const deleteImage = async (productId: number, imgId: number) => {
    return await del(`/products/${productId}/images/${imgId}`, {
      authType: AuthType.User,
    });
  };

  return {
    getImages,
    getImageById,
    createImage,
    updateImage,
    deleteImage
  };
};