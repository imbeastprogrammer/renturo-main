import { router } from '@inertiajs/react';
import AdminLayout from '@/layouts/AdminLayout';
import ListingFilter from '../listings/components/ListingFilter';
import BookingsTable from './components/BookingsTable';
import dummyBookings from '@/data/dummyBookings';
import { useSearchParams } from '@/hooks/useSearchParams';

const tabs = [
    { label: 'All Bookings', value: 'all' },
    { label: 'Done', value: 'done' },
    { label: 'Upcoming', value: 'upcoming' },
    { label: 'Canceled', value: 'canceled' },
];

function BookingsPage() {
    const { queryParams, searchParams } = useSearchParams();
    const filter = searchParams.get('filter');

    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4 rounded-lg border p-4 shadow-lg'>
            <ListingFilter
                value={filter || 'all'}
                data={tabs}
                onChange={(value) => {
                    router.visit(`/admin/post/bookings`, {
                        data: {
                            ...queryParams,
                            filter: value,
                        },
                    });
                }}
            />
            <BookingsTable bookings={dummyBookings} />
        </div>
    );
}

BookingsPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default BookingsPage;
