import type { User } from './user';
export type OrderStatus = 'pending' | 'processing' | 'delivering' | 'delivered' | 'cancelled';
export type AddressType = 'home' | 'work' | 'other';

export interface Order {
    id: string;
    customer_id: string;
    status: OrderStatus;
    full_name: string;
    phone_number: string;
    email: string;
    address_type: AddressType;
    province: string;
    district: string;
    ward: string;
    street_address: string;
    total_price: number;
    shipping_fee: number;
    final_total_amount: number;
    notes?: string;
    admin_note?: string;
    cancelled_reason?: string;
    cancelled_at?: string;
    cancelled_by?: string;
    expected_delivery_date?: string;
    items: OrderItem[];
    created_at: string;
    updated_at: string;
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: string;
    product_snapshot: Product_Snapshot;
    quantity: number;
    total_item_price: number;
    created_at: string;
    updated_at: string;
    flag: boolean;
}

export interface Product_Snapshot {
    id: string;
    product_name: string;
    user_full_name?: string | null;
    unit: string;
    price: number;
}

export interface OrderHistory {
    id: number;
    order_id: string;
    status: OrderStatus;
    notes: string;
    created_at: string;
    updated_at: string;
    changeable_type: 'App\\Models\\User' | 'App\\Models\\Customer';
    changeable: Pick<User, 'id' | 'email' | 'full_name' | 'phone_number'>;
}
