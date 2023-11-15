import { useState } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link } from '@inertiajs/react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Searchbar from './components/Searchbar';
import UserManagementTable from './components/UserManagementTable';
import Pagination from '@/components/super-admin/Pagination';

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
    const [currentPage, setCurrentPage] = useState(1);

    return (
        <div className='h-full p-4'>
            <div className='grid h-full grid-rows-[auto_1fr_auto] gap-4 rounded-xl bg-white p-4 shadow-lg'>
                <div className='flex items-center justify-between'>
                    <Searchbar />
                    <div className='flex items-center gap-4'>
                        <div>
                            <span className='text-[20px] font-semibold text-[#2E3436]/80'>
                                2
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
                        Showing 1 to 2 of 2 Users
                    </div>
                    <Pagination
                        currentPage={currentPage}
                        numberOfPages={users.last_page}
                        onNextPage={(page) => setCurrentPage(page + 1)}
                        onPrevPage={(page) => setCurrentPage(page - 1)}
                        onPageChange={(page) => setCurrentPage(page)}
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
