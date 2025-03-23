import { useApi, AuthType } from './useApi';
import type { User } from '~/types/user'; // Giả sử type User nằm trong '~/types/user'
import type { ApiResponse } from '~/types/api';

export const useUsers = () => {
    const { get, post, put, del } = useApi();

    // Lấy danh sách người dùng (có thể phân trang)
    const getUsers = async (
        page: number = 1,
        perPage: number = 100,
        authType: AuthType = AuthType.User
    ) => {
        return await get<User[]>('/users', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Tìm kiếm người dùng theo tiêu chí cơ bản
    const searchUsers = async (
        filters: {
            page?: number;
            per_page?: number;
            search?: string;
        },
        authType: AuthType = AuthType.User
    ) => {
        return await get<User[]>('/users/search', {
            params: { ...filters },
            authType,
        });
    };

    // Tìm kiếm người dùng với bộ lọc nâng cao
    const searchUsersWithFilters = async (
        filters: {
            page?: number;
            per_page?: number;
            search?: string;
            gender?: string;
            is_banned?: boolean;
            role_id?: number;
            start_date?: string;
            end_date?: string;
        },
        authType: AuthType = AuthType.User
    ) => {
        return await get<User[]>('/users/search-with-filters', {
            params: { ...filters },
            authType,
        });
    };

    // Lấy danh sách người dùng đã bị xóa mềm (trashed)
    const getTrashedUsers = async (
        page: number = 1,
        perPage: number = 10,
        authType: AuthType = AuthType.User
    ) => {
        return await get<User[]>('/users/trashed', {
            params: { page, per_page: perPage },
            authType,
        });
    };

    // Lấy thông tin một người dùng theo usercode
    const getUserByCode = async (
        usercode: string,
        authType: AuthType = AuthType.User
    ) => {
        return await get<User>(`/users/${usercode}`, {
            authType,
        });
    };

    // Lấy thông tin một người dùng theo id
    const getUserById = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await get<User>(`/users/${id}`, {
            authType,
        });
    };

    // Tạo người dùng mới
    const createUser = async (
        userData: FormData | Partial<User>,
        authType: AuthType = AuthType.User
    ) => {
        return await post<User>('/users/', userData, {
            authType,
        });
    };

    // Cập nhật thông tin người dùng
    const updateUser = async (
        id: number,
        userData: FormData | Partial<User>,
        authType: AuthType = AuthType.User
    ) => {
        return await put<User>(`/users/${id}`, userData, {
            authType,
        });
    };

    // Xóa mềm người dùng
    const deleteUser = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/users/${id}`, {
            authType,
        });
    };

    // Khôi phục người dùng từ thùng rác
    const restoreUser = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await put<User>(`/users/restore/${id}`, null, {
            authType,
        });
    };

    // Xóa vĩnh viễn người dùng
    const forceDeleteUser = async (
        id: number,
        authType: AuthType = AuthType.User
    ) => {
        return await del<null>(`/users/force-delete/${id}`, {
            authType,
        });
    };

    return {
        getUsers,
        searchUsers,
        searchUsersWithFilters,
        getTrashedUsers,
        getUserByCode,
        createUser,
        updateUser,
        deleteUser,
        restoreUser,
        forceDeleteUser,
        getUserById,
    };
};