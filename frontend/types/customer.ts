import type { Address } from './address';

export interface CustomerAddress {
    id: number;
    full_name: string;
    phone_number: string;
    address_type: string;
    address: Address;
}

export interface Customer {
    id: number;
    email: string;
    password: string;
    remember_token?: string;
    full_name: string;
    phone_number: string;
    gender?: string;
    date_of_birth?: string;
    total_orders: number;
    total_spending: number;
    last_order_date?: string;
    addresses?: CustomerAddress[];
    newsletter_subscribed: boolean;
    is_banned: boolean;
    verified_at?: string;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
    avatar_url?: string;
}
