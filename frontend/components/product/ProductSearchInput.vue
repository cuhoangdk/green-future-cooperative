<template>
    <div>
        <div class="relative">
            <input v-model="searchQuery" type="search" placeholder="Tìm kiếm..." @input="debouncedupdateQuery"
                class="pl-4 pr-12 py-2 rounded-full border-2 border-green-500 bg-green-700 text-white placeholder-green-200 focus:border-green-400 focus:outline-none w-full" />
            <!-- <div @click="updateQuery" class="btn btn-square btn-ghost rounded-full absolute right-2 top-1/2 transform -translate-y-1/2">
                <Search class="w-5 h-5" />
            </div> -->
            <Search @click="updateQuery"
                class="w-8 h-8 p-1.5 bg-green-500 text-white hover:bg-white hover:text-gray-800 transition-colors duration-150 rounded-full absolute right-2 top-1/2 transform -translate-y-1/2" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Search } from 'lucide-vue-next'
import { debounce } from 'lodash-es'

const router = useRouter()

const searchQuery = ref('')
const debouncedupdateQuery = debounce(updateQuery, 500)

function updateQuery () {
    if (router.currentRoute.value.name !== 'products') {
        router.push({
            name: 'products',
            query: { search: searchQuery.value },
        })
    } else {
        router.push({
            query: { search: searchQuery.value },
        })
    }
}
</script>