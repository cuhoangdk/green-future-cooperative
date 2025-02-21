<script setup lang="ts">
import { ref, defineEmits } from "vue";
import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next';
import { usePosts } from "#imports";
import type { Post } from "~/types/post";
import type { PaginationMeta, PaginationLinks } from "~/types/api";
import { useRuntimeConfig } from "#app";


const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;
const defaultImage = "/img/banner.png";

interface Props {
  posts: Post[];
  meta: PaginationMeta | null;
  links: PaginationLinks | null;
  isLoading: boolean;
}
const probs = defineProps<Props>();

const expandedRows = ref(new Set());

const emit = defineEmits(['page-change', 'post-deleted']);


// Trạng thái mở rộng của các hàng
const toggleRow = (id: number) => {
  const newSet = new Set(expandedRows.value);
  newSet.has(id) ? newSet.delete(id) : newSet.add(id);
  expandedRows.value = newSet;
};

const onPageChange = (page: number) => {
  emit('page-change', page);
};


const handleDeletePost = async (id: number) => {
  try {
    const { deletePost } = usePosts();
    await deletePost(id);
    // Đóng expanded row nếu đang mở
    if (expandedRows.value.has(id)) {
      expandedRows.value.delete(id);
    }
    // Emit event với id bài post đã xóa
    emit('post-deleted', id);
  } catch (error) {
    console.error('Error deleting post:', error);
  }
};

</script>

<template>
  <div>
    <!-- Loading Overlay -->
    <CommonLoading class="absolute inset-0 z-50 flex items-center justify-center bg-opacity-75"
      :isLoading="isLoading" />
    <!-- Table Section -->
    <div class="w-full overflow-x-auto mb-4">
      <table class="w-full border-collapse bg-white border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-gray-800">
            <th class="p-4 text-left" style="width: 5%;"></th>
            <th class="p-4 text-left" style="width: 35%;">Tiêu đề</th>
            <th class="p-4 text-left" style="width: 15%;">Danh mục</th>
            <th class="p-4 text-left" style="width: 15%;">Tác giả</th>
            <th class="p-4 text-left" style="width: 10%;">Hot</th>
            <th class="p-4 text-left" style="width: 10%;">Nổi bật</th>
            <th class="p-4 text-left" style="width: 10%;">Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <TransitionGroup name="list1">
            <template v-for="post in posts" :key="post.id">
              <tr class="list1-item border-b border-gray-100 hover:bg-gray-50 cursor-pointer" @click="toggleRow(post.id)"
                :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(post.id) }">
                <td class="p-3">
                  <component :is="expandedRows.has(post.id) ? ChevronDown : ChevronRight"
                    class="w-5 h-5 text-green-600" />
                </td>
                <td class="p-3">{{ post.title }}</td>
                <td class="p-3">{{ post.category?.name }}</td>
                <td class="p-3">{{ post.user?.full_name }}</td>
                <td class="p-3">
                  <span class="px-3 py-0.5 rounded-full text-sm"
                    :class="post.is_hot ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    {{ post.is_hot ? 'Có' : 'Không' }}
                  </span>
                </td>
                <td class="p-3">
                  <span class="px-3 py-0.5 rounded-full text-sm"
                    :class="post.is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    {{ post.is_featured ? 'Có' : 'Không' }}
                  </span>
                </td>
                <td class="p-3">
                  <span class="px-3 py-0.5 rounded-full text-sm" :class="post.post_status === 'published'
                    ? 'bg-green-100 text-green-800'
                    : 'bg-gray-100 text-gray-800'">
                    {{ post.post_status.charAt(0).toUpperCase() + post.post_status.slice(1) }}
                  </span>
                </td>
              </tr>
              <tr v-if="expandedRows.has(post.id)" class="list1-item bg-gray-50">
                <td colspan="7" class="p-4">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <img :src="`${backendUrl}${post.featured_image}`"
                        @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = defaultImage; }"
                        class="w-full aspect-video object-cover rounded border border-gray-200 bg-gray-100" alt="Cover"
                        loading="lazy" />
                    </div>
                    <div class="space-y-2 md:col-span-2 h-full flex flex-col justify-between">
                      <div>
                        <p class="font-semibold">Tóm tắt:</p>
                        <p class="text-gray-600 line-clamp-4">{{ post.summary }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                          <div>
                            <p class="font-semibold">Ngày tạo:</p>
                            <p class="text-gray-600">{{ new Date(post.created_at).toLocaleString('vi-VN') }}</p>
                          </div>
                          <div>
                            <p class="font-semibold">Ngày xuất bản:</p>
                            <p class="text-gray-600">
                              {{ post.published_at ? new Date(post.published_at).toLocaleString('vi-VN') : "Chưa xuất bản"}}
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="flex space-x-4 mt-auto">
                        <button class="px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                          Sửa
                        </button>
                        <button @click="handleDeletePost(post.id)"
                          class="px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                          Xóa
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </TransitionGroup>
        </tbody>
      </table>
      <div class="flex justify-end mr-4">
        <CommonAdvancePagination :links="links" :meta="meta" @page-change="onPageChange" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.list1-item {
  transition: all 0.05s ease;
}

.list1-enter-from,
.list1-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}
</style>