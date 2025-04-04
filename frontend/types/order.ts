export type OrderStatus = 'pending' | 'processing' | 'delivered' | 'cancelled';
export type AddressType = 'home' | 'work' | 'other';

export interface Order {
    id: number;
    order_code: string;
    customer_id: string;
    status: OrderStatus;
    full_name: string;
    phone_number: string;
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
    product_id: number;
    product_snapshot: Product_Snapshot;
    quantity: number;
    total_item_price: number;
    created_at: string;
    updated_at: string;
    flag: boolean;
}

export interface Product_Snapshot {
    id: number;
    product_code: string;
    product_name: string;
    user_full_name?: string | null;
    unit: string;
    price: string;
}