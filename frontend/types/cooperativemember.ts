export interface CooperativeMember {
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