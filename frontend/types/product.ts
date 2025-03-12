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