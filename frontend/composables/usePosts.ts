import { useApi } from './useApi'
import type { Post } from '~/types/post'
import type { ApiResponse } from '~/types/api'
import { useAuth } from './useAdminAuth';

export const usePosts = () => {
    const { get, post, put, del } = useApi()

    const fetchPosts = async (page: number = 1, per_page: number = 10, useAuth: boolean): Promise<ApiResponse<Post[]>> => {
        return await get<Post[]>('/posts', { page, per_page }, { useAuth })
    }

    const fetchPostByCategoryId = async (id: number, page: number = 1, per_page: number = 10): Promise<ApiResponse<Post[]>> => {
        return await get<Post[]>(`/posts/category/${id}`, { page, per_page })
    }

    const fetchPostByCategorySlug = async (slug: string, page: number = 1, per_page: number = 10): Promise<ApiResponse<Post[]>> => {
        return await get<Post[]>(`/posts/category-slug/${slug}`, { page, per_page })
    }

    const fetchHotPosts = async (): Promise<ApiResponse<Post[]>> => {
        return await get<Post[]>('/posts/hot')
    }

    const fetchFeaturedPosts = async (): Promise<ApiResponse<Post[]>> => {
        return await get<Post[]>('/posts/featured')
    }

    const fetchPostById = async (id: string) => {
        return await get(`/posts/${id}`)
    }

    const fetchPostBySlug = async (slug: string) => {
        return await get(`/posts/slug/${slug}`)
    }

    const createPost = async (postData: FormData) => {
        return await post('/posts', postData, undefined, { useAuth: true})
    }

    const updatePost = async (id: string, postData: FormData)=> {
        return await put(`/posts/${id}`, postData)
    }

    const deletePost = async (id: string)=> {
        return await del(`/posts/${id}`)
    }

    return {
        fetchPosts,
        fetchPostByCategoryId,
        fetchPostByCategorySlug,
        fetchPostById,
        fetchPostBySlug,
        fetchHotPosts,
        fetchFeaturedPosts,
        createPost,
        updatePost,
        deletePost,
    }
}