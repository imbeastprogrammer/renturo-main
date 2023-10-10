import { router } from '@inertiajs/react';
import AdminLayout from '@/layouts/AdminLayout';
import ListingFilter from '../listings/components/ListingFilter';
import CategoriesTable from './components/CategoriesTable';
import dummyCategories from '@/data/dummyCategories';

const tabs = [
    { label: 'All Categories', value: 'all' },
    { label: 'Approved', value: 'approved' },
    { label: 'To Review', value: 'review' },
    { label: 'Declined', value: 'declined' },
];

function CategoriesPage() {
    const searchParams = new URLSearchParams(window.location.search);
    const filter = searchParams.get('filter') || 'all';

    const handleChangeFilter = (value: string) => {
        router.visit(`/admin/post/categories?active=Post&filter=${value}`);
    };

    return (
        <div className='grid h-full grid-rows-[auto_auto_1fr] gap-y-4 rounded-lg border p-8 shadow-lg'>
            <h1 className='text-[30px] font-semibold leading-none'>
                Categories
            </h1>
            <ListingFilter
                value={filter}
                data={tabs}
                onChange={handleChangeFilter}
            />
            <CategoriesTable categories={dummyCategories} />
        </div>
    );
}

CategoriesPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default CategoriesPage;
