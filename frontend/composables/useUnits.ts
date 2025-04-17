import { useApi, AuthType } from './useApi';
import type { Unit } from '~/types/product';

export const useUnits = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách đơn vị sản phẩm (có thể phân trang)
    const getUnits = async (
        page: number = 1,
        perPage: number = 100,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Unit[]>('/product-units', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy danh sách đơn vị sản phẩm đã bị xóa mềm (trashed)
    const getTrashedUnits = async (
        page: number = 1,
        perPage: number = 10,
        authType: AuthType = AuthType.User
    ) => {
        return await get<Unit[]>('/product-units/trashed', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy thông tin một đơn vị sản phẩm theo id
    const getUnitById = async (
        id: number,
        authType: AuthType = AuthType.Guest
    ) => {
        return await get<Unit>(`/product-units/${id}`, {
            authType,
        });
    };

    // Tạo đơn vị sản phẩm mới
    const createUnit = async (
        unitData: FormData | Partial<Unit>,
        authType: AuthType = AuthType.User
    ) => {
        return await post<Unit>('/product-units', unitData, {
            authType,
        });
    };

    // Cập nhật thông tin đơn vị sản phẩm
    const updateUnit = async (
        id: number,
        unitData: FormData | Partial<Unit>,
        authType: AuthType = AuthType.User
    ) => {
        return await put<Unit>(`/product-units/${id}`, unitData, {
            authType,
        });
    };

    // Xóa mềm đơn vị sản phẩm
    const deleteUnit = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/product-units/${id}`, {
            authType,
        });
    };

    // Khôi phục đơn vị sản phẩm từ thùng rác
    const restoreUnit = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await put<Unit>(`/product-units/restore/${id}`, null, {
            authType,
        });
    };

    // Xóa vĩnh viễn đơn vị sản phẩm
    const forceDeleteUnit = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/product-units/force-delete/${id}`, {
            authType,
        });
    };

    return {
        getUnits,
        getTrashedUnits,
        getUnitById,
        createUnit,
        updateUnit,
        deleteUnit,
        restoreUnit,
        forceDeleteUnit,
    };
};