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
    cancelled_at?: Date;
    cancelled_by?: string;
    expected_delivery_date?: Date;
    created_at: Date;
    updated_at: Date;
}