import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useSearchParams } from '@/hooks/useSearchParams';

import { Listing } from '@/types/listings';
import AdminLayout from '@/layouts/AdminLayout';
import ListingsTable from './components/ListingsTable';
import ListingFilter from './components/ListingFilter';
import dummyListings from '@/data/dummyListings';
import Searchbar from '@/components/tenant/Searchbar';
import Pagination from '@/components/tenant/Pagination';
import { ScrollArea } from '@/components/ui/scroll-area';

const tabs = [
    { label: 'All Listings', value: 'all' },
    { label: 'Posted', value: 'posted' },
    { label: 'To Review', value: 'review' },
    { label: 'Declined', value: 'declined' },
];

type ListingsPageProps = {
    posts: Listing[];
};
function ListingsPage({ posts }: ListingsPageProps) {
    const { searchParams, queryParams } = useSearchParams();
    const filter = searchParams.get('filter');
    const [currentPage, setCurrentPage] = useState(1);

    return (
        <div className='-h-full grid grid-rows-[auto_auto_1fr] gap-y-4 overflow-hidden rounded-lg border p-4 shadow-lg'>
            <div className='flex items-center gap-2'>
                <Searchbar placeholder='Search for property, keyword, or owner' />
                <Searchbar placeholder='Search by category' />
                <Searchbar placeholder='Search by subcategory' />
                <Button className='w-[107px] bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                    Search
                </Button>
            </div>
            <ListingFilter
                value={filter || 'all'}
                data={tabs}
                onChange={(value) =>
                    router.visit(`/admin/post-management/list-of-properties`, {
                        data: { ...queryParams, filter: value },
                    })
                }
            />
            <ListingsTable listings={posts} />
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

ListingsPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default ListingsPage;
