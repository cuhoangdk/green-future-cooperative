import type { CartItem } from '~/types/cart';

export const useParametersStore = defineStore('parameters', () => {
    const parameters = ref<Record<string, string>>({})
    const { getCartItems } = useCart();
  
    async function fetchParameters() {
        const { data, status, refresh } = await getCartItems();
        const cartItems = computed<CartItem[]>(() => 
            Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []
        );
    }
  
    return { parameters, fetchParameters }
  })