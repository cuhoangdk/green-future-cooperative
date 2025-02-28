import { useApi } from './useApi'
import type { Post } from '~/types/post'

export const usePosts = () => {
  const { get, post, put, del } = useApi()

  // Lấy danh sách bài viết (phân trang)
  const getPosts = async (
    page: number = 1,
    perPage: number = 10,
    useToken: boolean = false
  ) => {
    return await get<Post>('/posts', {
      params: { page, per_page: perPage },
      useToken,
    })
  }

  // Lấy bài viết theo ID danh mục
  const getPostsByCategoryId = async (
    categoryId: number,
    page: number = 1,
    perPage: number = 10
  ) => {
    return await get<Post>(`/posts/category/${categoryId}`, {
      params: { page, per_page: perPage },
    })
  }

  // Lấy bài viết theo slug danh mục
  const getPostsByCategorySlug = async (
    categorySlug: string,
    page: number = 1,
    perPage: number = 10
  ) => {
    return await get<Post>(`/posts/category-slug/${categorySlug}`, {
      params: { page, per_page: perPage },
    })
  }

  // Lấy bài viết nổi bật (hot)
  const getHotPosts = async () => {
    return await get<Post>('/posts/hot')
  }

  // Lấy bài viết đặc sắc (featured)
  const getFeaturedPosts = async () => {
    return await get<Post>('/posts/featured')
  }

  // Lấy bài viết theo ID
  const getPostById = async (postId: string) => {
    return await get(`/posts/${postId}`)
  }

  // Lấy bài viết theo slug
  const getPostBySlug = async (slug: string) => {
    return await get(`/posts/slug/${slug}`)
  }

  // Tạo bài viết mới
  const createPost = async (postData: FormData) => {
    return await post('/posts', postData, {
      useToken: true,
      customHeaders: { 'Content-Type': 'multipart/form-data' }, // Nếu dùng FormData
    })
  }

  // Cập nhật bài viết
  const updatePost = async (postId: number, postData: FormData) => {
    return await put(`/posts/${postId}`, postData, {
      useToken: true,
      customHeaders: { 'Content-Type': 'multipart/form-data' }, // Nếu dùng FormData
    })
  }

  // Xóa bài viết
  const deletePost = async (postId: number) => {
    return await del(`/posts/${postId}`, {
      useToken: true,
    })
  }

  return {
    getPosts,
    getPostsByCategoryId,
    getPostsByCategorySlug,
    getHotPosts,
    getFeaturedPosts,
    getPostById,
    getPostBySlug,
    createPost,
    updatePost,
    deletePost,
  }
}