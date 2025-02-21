import { useApi } from './useApi'
import type { PostCategory } from '~/types/postcategory'
import type { ApiResponse } from '~/types/api'

export const usePostcategories = () => {
    const { get, post, put, del } = useApi()

    const fetchAllPostCategories = async (page: number = 1, per_page: number = 10): Promise<ApiResponse<PostCategory[]>> => {
        return await get<PostCategory[]>('/post-categories', { page, per_page });
    }

    const fetchPostCategoryById = async (id: string) => {
        return await get(`/post-categories/${id}`)
    }

    const createPostCategory = async (postData: PostCategory)=> {
        return await post('/post-categories', postData)
    }

    const updatePostCategory = async (id: string, postData: PostCategory)=> {
        return await put(`/post-categories/${id}`, postData)
    }

    const deletePostCategory = async (id: string)=> {
        return await del(`/post-categories/${id}`)
    }

    return {
        fetchAllPostCategories,
        fetchPostCategoryById,
        createPostCategory,
        updatePostCategory,
        deletePostCategory,
    }
}