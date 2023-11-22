import { useState } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link, router } from '@inertiajs/react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Searchbar from './components/Searchbar';
import UserManagementTable from './components/UserManagementTable';
import Pagination from '@/components/super-admin/Pagination';
import { useSearchParams } from '@/hooks/useSearchParams';

type UserManagementProps = {
    users: PaginatedUser;
};

type PaginatedUser = {
    current_page: number;
    data: User[];
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
};

function UserManagement({ users }: UserManagementProps) {
    const { pathname } = window.location;
    const [currentPage, setCurrentPage] = useState(1);
    const { searchParams } = useSearchParams();
    const searchTerm = searchParams.get('searchTerm') || '';

    const recordsCount = users.data.length;

    const handleNextPage = (page: number) => {
        if (page < users.last_page) setCurrentPage(page + 1);
    };
    const handlePrevPage = (page: number) => {
        if (page > 1) setCurrentPage(page - 1);
    };

    const handlePageChange = (page: number) => setCurrentPage(page);

    return (
        <div className='h-full p-4'>
            <div className='grid h-full grid-rows-[auto_1fr_auto] gap-4 rounded-xl bg-white p-4 shadow-lg'>
                <div className='flex items-center justify-between'>
                    <Searchbar
                        value={searchTerm}
                        onChange={(e) =>
                            router.replace(
                                `${pathname}?searchTerm=${e.target.value}`,
                            )
                        }
                    />
                    <div className='flex items-center gap-4'>
                        <div>
                            <span className='text-[20px] font-semibold text-[#2E3436]/80'>
                                {recordsCount}
                            </span>{' '}
                            <span className='text-[16px] text-[#2E3436]/50'>
                                Record(s) found
                            </span>
                        </div>
                        <div>
                            <Link href='/super-admin/administration/user-management/add'>
                                <Button className='gap-2 bg-[#84C58A] text-[15px] font-medium hover:bg-[#84C58A]/90'>
                                    <PlusIcon className='h-4 w-4' /> Create
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
                <ScrollArea>
                    <UserManagementTable users={users.data} />
                </ScrollArea>
                <div className='flex items-center justify-between'>
                    <div className='text-[15px] font-medium text-black/50'>
                        Showing {recordsCount} record(s) of page {currentPage}
                    </div>
                    <Pagination
                        currentPage={currentPage}
                        numberOfPages={users.last_page}
                        onNextPage={handleNextPage}
                        onPrevPage={handlePrevPage}
                        onPageChange={handlePageChange}
                    />
                    <div className='flex items-center gap-2 text-[15px] font-medium text-black/50'>
                        <span>Page</span>
                        <Input
                            value={currentPage}
                            className='h-8 w-16 text-center'
                            type='number'
                            onChange={(e) =>
                                setCurrentPage(Number(e.target.value))
                            }
                        />
                        <span>of {users.last_page}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}

UserManagement.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default UserManagement;
