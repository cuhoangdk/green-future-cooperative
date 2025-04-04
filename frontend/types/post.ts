import type { User } from "./user";

export interface Post {
    id: number;
    title: string;
    slug: string;
    summary: string;
    content: string;
    featured_image: string;
    category: PostCategory | null;
    user: User | null;
    post_status: string;
    is_hot: boolean;
    hot_order: number | null;
    is_featured: boolean;
    featured_order: number | null;
    tags: string;
    meta_title: string;
    meta_description: string;
    views: number;
    published_at: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface PostCategory {
    id: number;
    name: string;
    slug: string;
    description: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}