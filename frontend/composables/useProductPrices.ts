import { useApi, AuthType } from './useApi';
import type { ProductPrice } from '~/types/product'; // Giả sử bạn có type Farm

export const useProductPrices = () => {
  const { get, post, put, del } = useApi();

  const getPrices = async (
    productId: string,
    page: number = 1,
    perPage: number = 10,
    sortBy: string = 'created_at',
    sortDir: 'asc' | 'desc' = 'asc',
    authType: AuthType = AuthType.Guest
  ) => {
    return await get<ProductPrice>(`/products/${productId}/quantity-prices`, {
      params: { page, per_page: perPage, sort_by: sortBy, sort_direction: sortDir },
      authType,
    });
  };

  const getPriceById = async (productId: string, id: number, authType: AuthType = AuthType.Guest) => {
    return await get<ProductPrice>(`/products/${productId}/quantity-prices/${id}`, {
      authType,
    });
  };

  const createPrice = async (productId: string ,logData: FormData) => {
    return await post<ProductPrice>(`/products/${productId}/quantity-prices`, logData, {
      authType: AuthType.User,
    });
  };

  const updatePrice = async (productId: string, id: number, priceData: FormData) => {
    return await put<ProductPrice>(`/products/${productId}/quantity-prices/${id}`, priceData, {
      authType: AuthType.User,
    });
  };

  const deletePrice = async (productId: string, id: number) => {
    return await del(`/products/${productId}/quantity-prices/${id}`, {
      authType: AuthType.User,
    });
  };

  return {
    getPrices,
    getPriceById,
    createPrice,
    updatePrice,
    deletePrice
  };
};