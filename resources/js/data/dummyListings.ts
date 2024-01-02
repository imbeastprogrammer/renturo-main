import { Listing } from '@/types/listings';

const dummyListings: Listing[] = [
    {
        id: 1,
        user_id: 101,
        title: 'Cozy Apartment',
        description: 'A comfortable apartment with a great view',
        address: '123 Main Street, Cityville',
        status: 'posted',
        created_at: '2023-01-01T12:00:00Z',
        updated_at: null,
        deleted_at: null,
        images: [],
    },
    {
        id: 2,
        user_id: 102,
        title: 'Modern House',
        description: 'A stylish and modern house with all amenities',
        address: '456 Oak Avenue, Townsville',
        status: 'to review',
        created_at: '2023-02-15T10:30:00Z',
        updated_at: '2023-03-05T14:45:00Z',
        deleted_at: null,
        images: [],
    },
    {
        id: 3,
        user_id: 103,
        title: 'Rural Cottage',
        description: 'A quaint cottage in the countryside',
        address: '789 Farm Road, Villageland',
        status: 'declined',
        created_at: '2023-04-10T08:15:00Z',
        updated_at: null,
        deleted_at: null,
        images: [],
    },
];

export default dummyListings;
