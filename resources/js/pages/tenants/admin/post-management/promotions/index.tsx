import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Input } from '@/components/ui/input';
import { useSearchParams } from '@/hooks/useSearchParams';

import AdminLayout from '@/layouts/AdminLayout';
import ListingsTable from './components/ListingsTable';
import ListingFilter from './components/ListingFilter';
import dummyListings from '@/data/dummyListings';
import Pagination from '@/components/tenant/Pagination';

const tabs = [
    { label: 'All Listings', value: 'all' },
    { label: 'Posted', value: 'posted' },
    { label: 'To Review', value: 'review' },
    { label: 'Declined', value: 'declined' },
];

function Promotions() {
    const { searchParams, queryParams } = useSearchParams();
    const filter = searchParams.get('filter');
    const [currentPage, setCurrentPage] = useState(1);

    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4 rounded-lg border p-4 shadow-lg'>
            <ListingFilter
                value={filter || 'all'}
                data={tabs}
                onChange={(value) =>
                    router.visit(`/admin/post-management/promotions`, {
                        data: { ...queryParams, filter: value },
                    })
                }
            />
            <ListingsTable listings={dummyListings} />
            <div className='flex items-center justify-between'>
                <div className='text-sm'>
                    <span>Showing 1 to 3 of 3 users</span>
                </div>
                <Pagination
                    currentPage={currentPage || 1}
                    numberOfPages={100}
                    onNextPage={(page) => setCurrentPage(page + 1)}
                    onPrevPage={(page) => setCurrentPage(page - 1)}
                    onPageChange={(page) => setCurrentPage(page)}
                />
                <div className='flex items-center gap-2 text-sm'>
                    <span>Page</span>
                    <Input
                        value={currentPage}
                        className='h-8 w-16 text-center'
                        type='number'
                        onChange={(e) => setCurrentPage(Number(e.target.value))}
                    />
                    <span>of {100}</span>
                </div>
            </div>
        </div>
    );
}

Promotions.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Promotions;
