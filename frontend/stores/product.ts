import { defineStore } from 'pinia'
import type { Product } from '~/types/product';

export const useProductStore = defineStore('product', {
  state: () => ({
    selectedProduct: null as Product | null,
  }),
  actions: {
    setSelectedProduct(product: Product) {
      this.selectedProduct = product
    },
  },
})