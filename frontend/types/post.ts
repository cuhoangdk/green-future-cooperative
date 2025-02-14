import type { PostCategory } from "./postcategory";
import type { CooperativeMember } from "./cooperativemember";

export interface Post {
    id: number;
    slug: string;
    title: string;
    summary: string;
    content: string;
    featured_image: string;
    published_at: string;
    category: PostCategory | null;
    author: CooperativeMember | null;
    is_hot: boolean;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
}