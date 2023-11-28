import { Link, router } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PlusIcon } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

import { User } from '@/types/users';
import AdminLayout from '@/layouts/AdminLayout';
import AdminsTable from './components/AdminsTable';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import Pagination from '@/components/tenant/Pagination';

type AdminsProps = {
    admins: PaginatedAdmin;
};

type PaginatedAdmin = {
    current_page: number;
    data: User[];
    prev_page_url: string | null;
    next_page_url: string | null;
    last_page: number;
};

function Admins({ admins }: AdminsProps) {
    const { pathname } = window.location;
    const recordsCount = admins.data.length;

    const handleNextPage = () => {
        if (admins.next_page_url) router.replace(admins.next_page_url);
    };

    const handlePrevPage = () => {
        if (admins.prev_page_url) router.replace(admins.prev_page_url);
    };

    const handlePageChange = (page: number) => {
        router.replace(pathname, { data: { page } });
    };

    return (
        <div className='grid grid-rows-[auto_1fr_auto] gap-4 rounded-xl border bg-white p-4 shadow-lg'>
            <div className='flex justify-between'>
                <div className='flex gap-4'>
                    <div className='w-[336px]'>
                        <TableSearchbar placeholder='Search' />
                    </div>
                    <Button className='h-[40px] w-[100px] bg-metalic-blue hover:bg-metalic-blue/90'>
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
            <AdminsTable admins={admins.data} />
            <div className='flex items-center justify-between'>
                <div className='text-[15px] font-medium text-black/50'>
                    Showing {recordsCount} record(s) of page{' '}
                    {admins.current_page}
                </div>
                <Pagination
                    currentPage={admins.current_page}
                    numberOfPages={admins.last_page}
                    onNextPage={handleNextPage}
                    onPrevPage={handlePrevPage}
                    onPageChange={handlePageChange}
                />
                <div className='flex items-center gap-2 text-sm'>
                    <span>Page</span>
                    <Input
                        value={admins.current_page}
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

Admins.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Admins;
