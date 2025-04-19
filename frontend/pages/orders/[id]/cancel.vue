<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-1/3 bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Phần 3: Thông tin tài chính và khác -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Hủy đơn hàng</h3>
                    <div class="mb-4">
                        <label for="current-password" class="text-gray-700 font-semibold block mb-1">
                            Lý do hủy <span class="text-red-500">*</span>
                        </label>
                        <textarea id="current-password" v-model="form.cancelled_reason" 
                            class="textarea textarea-bordered textarea-primary w-full" 
                            placeholder="Lý do hủy" rows="4" required></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
                    <button type="button" class="btn" @click="$router.back()">
                        Quay lại
                    </button>
                    <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending' || !isFormValid">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                        Hủy đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
const route = useRoute();
const { cancelOrder } = useOrder();
const { $toast } = useNuxtApp();
const router = useRouter();
const id = String(route.params.id)
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

const form = ref({
    cancelled_reason: '',
});

const isFormValid = computed(() => {
    return form.value.cancelled_reason ;
});

const handleSubmit = async () => {
    try {
        status.value = 'pending';
        const { error } = await cancelOrder(id, form.value.cancelled_reason);

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Hủy đơn thành công!');
        router.push('/orders');
    } catch (error: any) {
        $toast.error('Có lỗi xảy ra trong quá trình hủy đơn hàng!');
    } finally {
        status.value = 'idle';
    }
};
</script>