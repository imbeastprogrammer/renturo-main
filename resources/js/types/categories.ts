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

export interface FormattedSubCategory {
    category_id: number;
    category_name: string;
    sub_category_id: number;
    sub_category_name: string;
}
