import type { Address } from './address';
import type { User } from './user';
export interface Farm {
    id: number;
    user_id: string;
    name: string;
    description: string | null;
    farm_size: number | null;
    soil_type: string | null;
    irrigation_method: string | null;
    latitude: number | null;
    longitude: number | null;
    address: Address;
    user: User;
    created_at: string;
    updated_at: string;
}