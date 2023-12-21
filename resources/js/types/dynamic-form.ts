interface Category {
    id: number;
    name: string;
}

interface SubCategory {
    id: number;
    name: string;
    category: Category;
}

export interface DynamicForm {
    id: number;
    name: string;
    description: string;
    subcategory: SubCategory;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
}
