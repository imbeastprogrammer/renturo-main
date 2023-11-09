export type Tenant = {
    id: string;
    name: string;
    status: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
    data: string | null;
    tenancy_db_name: string;
};
