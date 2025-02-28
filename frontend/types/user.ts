export interface User {
    id: number;
    email: string;
    full_name: string;
    gender: string;
    date_of_birth: Date | null;
    phone_number: string;
    avatar_url: string;
    bio: string;
    last_login_at: Date | null;
    created_at: Date;
    updated_at: Date;
    usercode: string;
    is_super_admin: boolean;
    is_banned: boolean;
    province: string | null;
    district: string | null;
    ward: string | null;
    street_address: string | null;
    deleted_at: Date | null;
    remember_token: string | null;
    role_id: number | null;
}