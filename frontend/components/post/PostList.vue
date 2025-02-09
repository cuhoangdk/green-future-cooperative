<template>
  <section class="w-full mt-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-left text-xl font-bold text-green-800">{{ title }}</h2>
      <div class="flex-1 h-[3px] bg-green-500 mx-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-5">
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>
  </section>
  <CommonBasePagination />
</template>

<script setup lang="ts">
interface Post {
  id: number
  title: string
  content: string
  image: string
  date: string
}

interface Props {
  title: string
  posts: Post[]
}

defineProps<Props>()
</script>