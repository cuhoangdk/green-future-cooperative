import { useApi, AuthType } from './useApi';
import type { ProductImage } from '~/types/product'; // Giả sử bạn có type Farm

export const useProductImages = () => {
  const { get, post, put, del } = useApi();

  const getImages = async (
    productId: string,
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

  const getImageById = async (productId: string, imgId: number, authType: AuthType = AuthType.Guest) => {
    return await get<ProductImage>(`/products/${productId}/images/${imgId}`, {
      authType,
    });
  };

  const createImage = async (productId: string ,imgData: FormData) => {
    return await post<ProductImage>(`/products/${productId}/images`, imgData, {
      authType: AuthType.User,
    });
  };

  const updateImage = async (productId: string, imgId: number, imgData: FormData) => {
    return await put<ProductImage>(`/products/${productId}/images/${imgId}`, imgData, {
      authType: AuthType.User,
    });
  };

  const deleteImage = async (productId: string, imgId: number) => {
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