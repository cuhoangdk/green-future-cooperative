export interface Notification {
    id: number;
    user_type: string;
    user_id?: number;
    title: string;
    type: string;
    is_read: boolean;
    read_at?: string;
    created_at?: string;
    updated_at?: string;
}