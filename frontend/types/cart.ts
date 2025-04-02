import type { Product } from '~/types/product';

export interface CartItem {
    id: number;
    customer_id: string;
    product: Product;
    quantity: number;
    purchase_price: number;
    invalid_quantity: boolean;
    invalid_message: string | null;
    created_at: string;
    updated_at: string;
}