export type User = {
    id: number;
    first_name: string;
    last_name: string;
    mobile_no: string;
    role: string;
    email: string;
    email_verified_at: string;
    created_at: string;
    verified_mobile_no: {
        id: number;
        user_id: number;
        mobile_no: string;
        code: string;
        verified_at: string;
        expires_at: string;
        created_at: string;
        updated_at: string;
    };
};
