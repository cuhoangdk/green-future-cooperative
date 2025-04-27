export interface ContactMessage {
    id: number;
    name: string;
    email: string;
    phone: string;
    gender?: 'male' | 'female' | 'other';
    message: string;
    created_at: string;
    updated_at: string;
}