import type { User } from './user';

export interface Unit {
    id: number;
    name: string;
    description?: string;
    allow_decimal: boolean;
    created_at?: Date;
    updated_at?: Date;
    deleted_at?: Date;
}

export interface ProductCategory {
    id: number;
    name: string;
    slug: string;
    description?: string;
    created_at?: Date;
    updated_at?: Date;
    deleted_at?: Date;
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
    sown_at?: Date;
    harvested_at?: Date;
    pricing_type: string;
    stock_quantity: number;
    status: string;
    sold_quantity: number;
    views: number;
    expired: number;
    meta_title?: string;
    meta_description?: string;
    meta_keyword?: string;
    created_at?: Date;
    updated_at?: Date;
    deleted_at?: Date;
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
    created_at?: Date;
    updated_at?: Date;
    product: Product;
}

export interface ProductImage {
    id: number;
    product_id: number;
    image_url: string;
    sort_order: number;
    is_primary: boolean;
    title?: string;
    created_at?: Date;
    updated_at?: Date;
}

export interface ProductPrice {
    id: number;
    product_id: number;
    quantity: number;
    price: number;
    created_at?: Date;
    updated_at?: Date;
    deleted_at?: Date;
}