import type { Address } from "./address";

export interface User {
    id: number;
    email: string;
    bank_account_number: string;
    bank_name: string;
    full_name: string;
    gender: string;
    date_of_birth: string | null;
    phone_number: string;
    avatar_url: string;
    bio: string;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    usercode: string;
    is_super_admin: boolean;
    is_banned: boolean;
    address: Address;
    deleted_at: string | null;
    remember_token: string | null;
    role_id: number | null;
}