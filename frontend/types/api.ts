export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface ApiResponse<T> {
    data: T;
    original?: any;
    links?: PaginationLinks;
    meta?: PaginationMeta;
    message?: string;
    status?: string;
    errors?: Record<string, string[]>;
    [key: string]: any;
}
