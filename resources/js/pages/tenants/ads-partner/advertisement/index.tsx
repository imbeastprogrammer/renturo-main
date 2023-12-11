import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import TableSearchbar from '@/components/tenant/TableSearchbar';
import PromotionsTable from './components/PromotionsTable';
import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';

function Advertisement() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4'>
            <div className='flex items-center justify-between gap-2'>
                <h1 className='text-[48px] font-semibold'>Advertisements</h1>
                <div className='flex gap-4'>
                    <div className='min-w-[330px]'>
                        <TableSearchbar placeholder='Search for property, keyword, or owner' />
                    </div>
                    <Button className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                        Search
                    </Button>
                    <Link href='/ads-partner/advertisement/create'>
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
            </div>
            <div className='rounded-lg border bg-white shadow-lg'>
                <PromotionsTable />
            </div>
        </div>
    );
}

Advertisement.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Advertisement;
