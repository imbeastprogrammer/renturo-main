import { router } from '@inertiajs/react';

import AdminLayout from '@/layouts/AdminLayout';
import ListingsTable from './components/ListingsTable';
import ListingFilter from './components/ListingFilter';
import dummyListings from '@/data/dummyListings';
import { useSearchParams } from '@/hooks/useSearchParams';

const tabs = [
    { label: 'All Listings', value: 'all' },
    { label: 'Posted', value: 'posted' },
    { label: 'To Review', value: 'review' },
    { label: 'Declined', value: 'declined' },
];

function ListingsPage() {
    const { searchParams, queryParams } = useSearchParams();
    const filter = searchParams.get('filter');

    return (
        <div className='-h-full grid grid-rows-[auto_auto_1fr] gap-y-4 rounded-lg border p-8 shadow-lg'>
            <div className='flex items-end gap-4'>
                <h1 className='text-[30px] font-semibold leading-none'>
                    Listings
                </h1>
                <span className='text-[15px] font-semibold text-gray-500'>
                    3 Listings found
                </span>
            </div>
            <ListingFilter
                value={filter || 'all'}
                data={tabs}
                onChange={(value) => {
                    router.visit(`/admin/post`, {
                        data: { ...queryParams, filter: value },
                    });
                }}
            />
            <ListingsTable listings={dummyListings} />
        </div>
    );
}

ListingsPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default ListingsPage;
