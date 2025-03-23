import { useApi, AuthType } from './useApi';
import type { Product } from '~/types/product';

export const useProducts = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách sản phẩm (phân trang)
    const getProducts = async (
        page: number = 1,
        perPage: number = 10,
        authType: AuthType = AuthType.Guest
    ) => {
        return await get<Product>('/products', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy sản phẩm theo ID danh mục
    const getProductsByCategoryId = async (
        categoryId: number,
        page: number = 1,
        perPage: number = 10
    ) => {
        return await get<Product>(`/products/category/${categoryId}`, {
            params: { page, per_page: perPage },
        });
    };

    // Lấy sản phẩm theo slug danh mục
    const getProductsByCategorySlug = async (
        categorySlug: string,
        page: number = 1,
        perPage: number = 10
    ) => {
        return await get<Product>(`/products/category-slug/${categorySlug}`, {
            params: { page, per_page: perPage },
        });
    };


    // Lấy sản phẩm theo ID
    const getProductById = async (productId: number) => {
        return await get<Product>(`/products/${productId}`);
    };

    // Lấy sản phẩm theo slug
    const getProductBySlug = async (slug: string) => {
        return await get<Product>(`/products/slug/${slug}`);
    };

    // Tạo sản phẩm mới
    const createProduct = async (productData: FormData) => {
        return await post('/products', productData, {
            authType: AuthType.User,
        });
    };

    // Cập nhật sản phẩm
    const updateProduct = async (productId: number, productData: FormData) => {
        return await put(`/products/${productId}`, productData, {
            authType: AuthType.User,
        });
    };

    // Xóa sản phẩm
    const deleteProduct = async (productId: number) => {
        return await del(`/products/${productId}`, {
            authType: AuthType.User,
        });
    };
    // Lấy QR code của sản phẩm
    const getProductQRCode = async (productId: number) => {
        return await get<string>(`/products/${productId}/qrcode`, {
            authType: AuthType.User,
        });
    };

    // Tìm kiếm sản phẩm theo các tiêu chí lọc
    const searchProducts = async (filters: {
        page?: number,
        per_page?: number,
        sort_by?: string,
        sort_direction?: string,
        search?: string,
        user_id?: number,
        category_id?: number,
        status?: string,
        name?: string,
        product_code?: string,
        description?: string,
        unit_id?: number,
        farm_id?: number,
        pricing_type?: string,
        is_active?: boolean,
        stock_quantity_min?: number,
        stock_quantity_max?: number,
        sown_at_from?: string,
        sown_at_to?: string,
        harvested_at_from?: string,
        harvested_at_to?: string,
    }, authType: AuthType = AuthType.User) => {
        return await get<Product>('/products/search-with-filters', {
            params: { ...filters, sort_by: filters.sort_by ?? 'updated_at' },
            authType,
        });
    };

    return {
        getProducts,
        getProductsByCategoryId,
        getProductsByCategorySlug,
        getProductById,
        getProductBySlug,
        createProduct,
        updateProduct,
        deleteProduct,
        searchProducts,
        getProductQRCode,
    };
};