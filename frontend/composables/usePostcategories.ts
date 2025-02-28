import { useApi } from './useApi'
import type { PostCategory } from '~/types/post'

export const usePostCategories = () => {
  const { get, post, put, del} = useApi() // Đổi 'del' thành 'deleteRequest' để tránh từ khóa 'delete'

  // Lấy tất cả danh mục bài viết
  const getAllPostCategories = async () => {
    return await get<PostCategory>('/post-categories')
  }

  // Lấy danh mục bài viết theo ID
  const getPostCategoryById = async (categoryId: string) => {
    return await get<PostCategory>(`/post-categories/${categoryId}`)
  }

  // Tạo danh mục bài viết mới
  const createPostCategory = async (categoryData: PostCategory) => {
    return await post('/post-categories', categoryData, {
      useToken: true, // Mặc định yêu cầu token cho hành động tạo
    })
  }

  // Cập nhật danh mục bài viết
  const updatePostCategory = async (categoryId: string, categoryData: PostCategory) => {
    return await put(`/post-categories/${categoryId}`, categoryData, {
      useToken: true, // Mặc định yêu cầu token cho hành động cập nhật
    })
  }

  // Xóa danh mục bài viết
  const deletePostCategory = async (categoryId: string) => {
    return await del(`/post-categories/${categoryId}`, {
      useToken: true, // Mặc định yêu cầu token cho hành động xóa
    })
  }

  return {
    getAllPostCategories,
    getPostCategoryById,
    createPostCategory,
    updatePostCategory,
    deletePostCategory,
  }
}