import { useApi } from './useApi'
import type { PostCategory } from '~/types/postcategory'
import type { ApiResponse } from '~/types/api'

export const usePostcategories = () => {
    const { get, post, put, del } = useApi()

    const fetchAllPostCategories = async (page: number = 1, per_page: number = 10): Promise<ApiResponse<PostCategory[]>> => {
        return await get<PostCategory[]>('/postcategories', { page, per_page });
    }

    const fetchPostCategoryById = async (id: string) => {
        return await get(`/postcategories/${id}`)
    }

    const createPostCategory = async (postData: PostCategory)=> {
        return await post('/postcategories', postData)
    }

    const updatePostCategory = async (id: string, postData: PostCategory)=> {
        return await put(`/postcategories/${id}`, postData)
    }

    const deletePostCategory = async (id: string)=> {
        return await del(`/postcategories/${id}`)
    }

    return {
        fetchAllPostCategories,
        fetchPostCategoryById,
        createPostCategory,
        updatePostCategory,
        deletePostCategory,
    }
}