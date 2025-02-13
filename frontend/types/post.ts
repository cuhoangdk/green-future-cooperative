interface Category {
    id: number;
    name: string;
    slug: string;
    description: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

interface Author {
    id: number;
    username: string;
    slug: string;
    email: string;
    full_name: string;
    phone_number: string;
    address: string;
    farm_location: string;
    farm_size: string;
    bank_account_number: string;
    bank_name: string;
    avatar_url: string;
    bio: string;
    is_active: boolean;
    verified_at: string;
    joined_at: string;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Post {
    id: number;
    slug: string;
    title: string;
    summary: string;
    content: string;
    image: string;
    date: string;
    category: Category | null;
    author: Author | null;
    is_hot: boolean;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
}