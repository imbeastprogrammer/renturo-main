import { Link } from '@inertiajs/react';
import { useState } from 'react';
import { PlusIcon } from 'lucide-react';
import { User } from '@/types/users';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

import AdminLayout from '@/layouts/AdminLayout';
import UsersTable from './components/UsersTable';
import Pagination from '@/components/tenant/Pagination';
import TableSearchbar from '@/components/tenant/TableSearchbar';

type UsersPageProps = {
    users: User[];
};

function UsersPage({ users }: UsersPageProps) {
    const [currentPage, setCurrentPage] = useState(1);
    const handleNextPage = (page: number) => setCurrentPage(page + 1);
    const handlePrevPage = (page: number) => setCurrentPage(page - 1);
    const handlePageChange = (page: number) => setCurrentPage(page);

    return (
        <div className='grid h-full grid-rows-[auto_1fr_auto] gap-y-4 overflow-hidden rounded-lg border p-8 shadow-lg'>
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
                    <Link href='/admin/user-management/users/create?active=Users'>
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
            <UsersTable users={users} />
            <div className='flex items-center justify-between'>
                <div className='text-sm'>
                    <span>Showing 1 to 3 of 3 users</span>
                </div>
                <Pagination
                    currentPage={currentPage || 1}
                    numberOfPages={100}
                    onNextPage={handleNextPage}
                    onPrevPage={handlePrevPage}
                    onPageChange={handlePageChange}
                />
                <div className='flex items-center gap-2 text-sm'>
                    <span>Page</span>
                    <Input
                        value={currentPage || 1}
                        className='h-8 w-16 text-center'
                        type='number'
                        onChange={(e) =>
                            handlePageChange(Number(e.target.value))
                        }
                    />
                    <span>of {100}</span>
                </div>
            </div>
        </div>
    );
}

UsersPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default UsersPage;
