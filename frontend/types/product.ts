import type { User } from './user';

export interface Unit {
    id: number;
    name: string;
    description?: string;
    allow_decimal: boolean;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
}

export interface ProductCategory {
    id: number;
    name: string;
    slug: string;
    description?: string;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
}

export interface Product {
    id: number;
    product_code: string;
    user_id: number;
    farm_id: number;
    category_id: number;
    unit: Unit;
    name: string;
    slug: string;
    description?: string;
    images?: ProductImage[];
    prices?: ProductPrice[];
    user?: User;
    seed_supplier?: string;
    cultivated_area?: number;
    sown_at: string;
    harvested_at?: string;
    pricing_type: string;
    stock_quantity: number;
    status: string;
    sold_quantity: number;
    views: number;
    expired: number;
    meta_title?: string;
    meta_description?: string;
    meta_keyword?: string;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
}

export interface CultivationLog {
    id: number;
    product_id: number;
    activity: string;
    fertilizer_used?: string | null;
    pesticide_used?: string | null;
    image_url?: string;
    video_url?: string;
    notes?: string;
    created_at?: string;
    updated_at?: string;
    product: Product;
}

export interface ProductImage {
    id: number;
    product_id: number;
    image_url: string;
    sort_order: number;
    is_primary: boolean;
    title?: string;
    created_at?: string;
    updated_at?: string;
}

export interface ProductPrice {
    id: number;
    product_id: number;
    quantity: number;
    price: number;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string;
}