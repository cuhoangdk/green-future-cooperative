export type UserType = 'member' | 'customer' | 'system';

export interface Log {
    id: number;
    user_type: UserType;
    user_id?: number | null;
    action: string;
    entity_type: string;
    old_data?: string | null;
    new_data?: string | null;
    ip_address?: string | null;
    user_agent?: string;
    created_at: string;
    updated_at: string;
}