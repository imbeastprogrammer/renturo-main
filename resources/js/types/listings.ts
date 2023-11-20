type ListingImage = {
    id: number;
    url: string;
    imageable_id: number;
    imageable_type: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
};

export type Listing = {
    id: number;
    user_id: number;
    title: string;
    description: string;
    address: string;
    status: string;
    created_at: string;
    updated_at: string | null;
    deleted_at: string | null;
    images: ListingImage[];
};
