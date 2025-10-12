import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import OwnerLayout from '@/layouts/OwnerLayout';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import PromotionsTable from './components/PromotionsTable';

function Promotions() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4 rounded-xl border p-4 shadow-xl'>
            <div className='flex justify-between gap-2'>
                <div className='flex gap-2'>
                    <div className='min-w-[591px]'>
                        <TableSearchbar placeholder='Search for property, keyword, or owner' />
                    </div>
                    <Button className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                        Search
                    </Button>
                </div>
                <Link href='/owner/post-management/analytics/promotions/create'>
                    <Button
                        type='button'
                        variant='outline'
                        className='items-center gap-2 border-metalic-blue text-[15px] font-medium text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
                    >
                        <PlusIcon className='h-4 w-4' />
                        Create
                    </Button>
                </Link>
            </div>
            <PromotionsTable />
        </div>
    );
}

Promotions.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default Promotions;
