import { User } from './users';

export type Tenant = {
    id: string;
    company: string;
    status: string;
    plan_type: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
    data: null;
    created_by: User | null;
    updated_by: User | null;
    deleted_by: User | null;
    tenancy_db_name: string;
};

type Domain = {
    id: number;
    domain: string;
    tenant_id: string;
    created_at: string;
    updated_at: string | null;
};

export type TenantWithDomains = Tenant & { domains: Domain[] };
