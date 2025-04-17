import { useApi, AuthType } from './useApi';
import type { ProductCategory } from '~/types/product';

export const useProductCategories = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách đơn vị sản phẩm (có thể phân trang)
    const getProductCategories = async (
        page: number = 1,
        perPage: number = 100,
        authType: AuthType = AuthType.User
    ) => {
        return await get<ProductCategory[]>('/product-categories', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy danh sách đơn vị sản phẩm đã bị xóa mềm (trashed)
    const getTrashedProductCategories = async (
        page: number = 1,
        perPage: number = 10,
        authType: AuthType = AuthType.User
    ) => {
        return await get<ProductCategory[]>('/product-categories/trashed', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy thông tin một đơn vị sản phẩm theo id
    const getProductCategoryById = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await get<ProductCategory>(`/product-categories/${id}`, {
            authType,
        });
    };

    // Tạo đơn vị sản phẩm mới
    const createProductCategory = async (
        unitData: FormData | Partial<ProductCategory>,
        authType: AuthType = AuthType.User
    ) => {
        return await post<ProductCategory>('/product-categories', unitData, {
            authType,
        });
    };

    // Cập nhật thông tin đơn vị sản phẩm
    const updateProductCategory = async (
        id: number,
        unitData: FormData | Partial<ProductCategory>,
        authType: AuthType = AuthType.User
    ) => {
        return await put<ProductCategory>(`/product-categories/${id}`, unitData, {
            authType,
        });
    };

    // Xóa mềm đơn vị sản phẩm
    const deleteProductCategory = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/product-categories/${id}`, {
            authType,
        });
    };

    // Khôi phục đơn vị sản phẩm từ thùng rác
    const restoreProductCategory = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await put<ProductCategory>(`/product-categories/restore/${id}`, null, {
            authType,
        });
    };

    // Xóa vĩnh viễn đơn vị sản phẩm
    const forceDeleteProductCategory = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/product-categories/force-delete/${id}`, {
            authType,
        });
    };

    return {
        getProductCategories,
        getTrashedProductCategories,
        getProductCategoryById,
        createProductCategory,
        updateProductCategory,
        deleteProductCategory,
        restoreProductCategory,
        forceDeleteProductCategory
    };
};