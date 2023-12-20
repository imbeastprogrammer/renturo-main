export interface Category {
    id: number;
    name: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
    sub_categories: SubCategory[];
}

export interface SubCategory {
    id: number;
    category_id: number;
    name: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
}
