import type { Post } from "./post";

export interface PostCategory {
    id: number;
    name: string;
    slug: string;
    description: string;
    is_active: boolean;
    created_at: Date;
    updated_at: Date;
    deleted_at: Date | null;
    posts: Post[];
}