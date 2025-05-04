<template>
    <div class="p-4 max-w-xl mx-auto">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Phần 3: Thông tin tài chính và khác -->
            <div>
                <div class="mb-4">
                    <label for="current-password" class="text-gray-700 font-semibold block mb-1">
                        Lý do hủy <span class="text-red-500">*</span>
                    </label>
                    <textarea id="current-password" v-model="form.cancelled_reason"
                        class="textarea textarea-bordered textarea-primary w-full" placeholder="Lý do hủy" rows="4"
                        required></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
                <UiButtonBack />
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending' || !isFormValid">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Hủy đơn
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Hủy đơn hàng', description: 'Quản lý đơn hàng' })

const route = useRoute();
const { cancelAdminOrder } = useAdminOrder();
const { $toast } = useNuxtApp();
const router = useRouter();
const orderId = String(route.params.id);
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

const form = ref({
    cancelled_reason: '',
});

const isFormValid = computed(() => {
    return form.value.cancelled_reason;
});

const handleSubmit = async () => {
    try {
        status.value = 'pending';
        const { error } = await cancelAdminOrder(orderId, form.value.cancelled_reason);

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Hủy đơn thành công!');
        router.push('/admin/orders');
    } catch (error: any) {
        $toast.error('Có lỗi xảy ra trong quá trình hủy đơn hàng!');
    } finally {
        status.value = 'idle';
    }
};
</script>