import { Link } from '@inertiajs/react';
import { ReactNode, useState } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { User } from '@/types/users';
import AdminLayout from '@/layouts/AdminLayout';
import OwnersTable from './components/OwnersTable';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import Pagination from '@/components/tenant/Pagination';

type OwnersProps = {
    owners: User[];
};
function Owners({ owners }: OwnersProps) {
    const [currentPage, setCurrentPage] = useState(1);

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
                    <Link href='/admin/user-management/owners/create?active=Users'>
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
            <OwnersTable owners={owners} />
            <div className='flex items-center justify-between'>
                <div className='text-sm'>
                    <span>Showing 1 to 3 of 3 users</span>
                </div>
                <Pagination
                    currentPage={currentPage}
                    numberOfPages={10}
                    onNextPage={(page) => setCurrentPage(page + 1)}
                    onPrevPage={(page) => setCurrentPage(page - 1)}
                    onPageChange={(page) => setCurrentPage(page)}
                />
                <div className='flex items-center gap-2 text-sm'>
                    <span>Page</span>
                    <Input
                        value={currentPage || 1}
                        className='h-8 w-16 text-center'
                        type='number'
                        onChange={(e) => setCurrentPage(Number(e.target.value))}
                    />
                    <span>of {10}</span>
                </div>
            </div>
        </div>
    );
}

Owners.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Owners;
