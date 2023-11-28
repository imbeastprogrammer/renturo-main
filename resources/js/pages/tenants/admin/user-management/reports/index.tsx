import { Link, router } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PlusIcon } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

import { useSearchParams } from '@/hooks/useSearchParams';
import AdminLayout from '@/layouts/AdminLayout';
import ReportsTable from './components/ReportsTable';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import Pagination from '@/components/tenant/Pagination';

type AdminsProps = {
    reports: [];
};

// type PaginatedAdmin = {
//     current_page: number;
//     data: [];
//     prev_page_url: string | null;
//     next_page_url: string | null;
//     last_page: number;
// };

function Reports({ reports }: AdminsProps) {
    const { pathname } = window.location;
    const { searchParams } = useSearchParams();

    const searchTerm = searchParams.get('searchTerm') || '';

    // const recordsCount = reports.data.length;

    // const handleNextPage = () => {
    //     if (reports.next_page_url) router.replace(reports.next_page_url);
    // };

    // const handlePrevPage = () => {
    //     if (reports.prev_page_url) router.replace(reports.prev_page_url);
    // };

    const handlePageChange = (page: number) => {
        router.replace(pathname, { data: { page } });
    };

    const onSearch = (searchText: string) => {
        router.replace(pathname, { data: { searchTerm: searchText } });
    };

    return (
        <div className='grid grid-rows-[auto_1fr_auto] gap-4 rounded-xl border bg-white p-4 shadow-lg'>
            <div className='flex justify-between'>
                <div className='flex gap-4'>
                    <div className='w-[336px]'>
                        <TableSearchbar
                            value={searchTerm}
                            placeholder='Search'
                            onChange={(e) => onSearch(e.target.value)}
                        />
                    </div>
                    <Button
                        onClick={() => router.reload()}
                        className='h-[40px] w-[100px] bg-metalic-blue hover:bg-metalic-blue/90'
                    >
                        Search
                    </Button>
                </div>
                <div>
                    <Link href='/admin/user-management/admins/create'>
                        <Button
                            variant='outline'
                            className='h-[40px] w-[100px] border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue/90'
                        >
                            <PlusIcon className='mr-2 h-4 w-4' />
                            Create
                        </Button>
                    </Link>
                </div>
            </div>
            <ReportsTable reports={reports} />
            <div className='flex items-center justify-between'>
                <div className='text-[15px] font-medium text-black/50'>
                    Showing {0} record(s) of page {0}
                </div>
                <Pagination
                    currentPage={1}
                    numberOfPages={1}
                    onNextPage={() => {}}
                    onPrevPage={() => {}}
                    onPageChange={handlePageChange}
                />
                <div className='flex items-center gap-2 text-sm'>
                    <span>Page</span>
                    <Input
                        value={1}
                        className='h-8 w-16 text-center'
                        type='number'
                        readOnly
                    />
                    <span>of {10}</span>
                </div>
            </div>
        </div>
    );
}

Reports.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Reports;
