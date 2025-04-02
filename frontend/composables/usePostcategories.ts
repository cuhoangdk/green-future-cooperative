import { useApi, AuthType } from './useApi';
import type { PostCategory } from '~/types/post'

export const usePostCategories = () => {
  const { get, post, put, del} = useApi() // Đổi 'del' thành 'deleteRequest' để tránh từ khóa 'delete'

  // Lấy tất cả danh mục bài viết
  const getAllPostCategories = async (authType: AuthType = AuthType.Guest) => {
    return await get<PostCategory>('/post-categories', {
      authType, // Không cần token để lấy danh sách danh mục
    })
  }

  // Lấy danh mục bài viết theo ID
  const getPostCategoryById = async (categoryId: number) => {
    return await get<PostCategory>(`/post-categories/${categoryId}`)
  }

  // Tạo danh mục bài viết mới
  const createPostCategory = async (categoryData: FormData) => {
    return await post('/post-categories', categoryData, {
      authType: AuthType.User, // Mặc định yêu cầu token cho hành động tạo
      headers: {
        'Content-Type': 'multipart/form-data', // Đảm bảo gửi dưới dạng form data
      },
    })
  }

  // Cập nhật danh mục bài viết
  const updatePostCategory = async (categoryId: number, categoryData: FormData) => {
    return await put(`/post-categories/${categoryId}`, categoryData, {
      authType: AuthType.User, // Mặc định yêu cầu token cho hành động tạo
      headers: {
        'Content-Type': 'multipart/form-data', // Đảm bảo gửi dưới dạng form data
      },
    })
  }

  // Xóa danh mục bài viết
  const deletePostCategory = async (categoryId: number) => {
    return await del(`/post-categories/${categoryId}`, {
      authType: AuthType.User, // Mặc định yêu cầu token cho hành động tạo
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