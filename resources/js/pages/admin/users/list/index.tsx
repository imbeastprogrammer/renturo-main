import { useState } from 'react';
import { User } from '@/types/users';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/layouts/AdminLayout';
import UsersTable from './components/UsersTable';
import Pagination from '@/components/Pagination';

type UsersPageProps = {
    users: User[];
};

function UsersPage({ users }: UsersPageProps) {
    const [currentPage, setCurrentPage] = useState(1);
    const handleNextPage = (page: number) => setCurrentPage(page + 1);
    const handlePrevPage = (page: number) => setCurrentPage(page - 1);
    const handlePageChange = (page: number) => setCurrentPage(page);

    return (
        <div className='grid h-full grid-rows-[auto_auto_1fr_auto] gap-y-4 rounded-lg border p-8 shadow-lg'>
            <p className='text-[15px] text-gray-500'>
                Users / User Management / List of Users
            </p>
            <h1 className='text-[30px] font-semibold leading-none'>
                List of Users
            </h1>
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
