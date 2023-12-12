import { ReactNode, useState } from 'react';
import { Button } from '@/components/ui/button';

import TableSearchbar from '@/components/tenant/TableSearchbar';
import PaymentsTable from './components/PaymentsTable';
import AdsPartnerLayout from '@/layouts/AdsPartnerLayout';
import Pagination from '@/components/tenant/Pagination';
import { Input } from '@/components/ui/input';

function Payments() {
    const recordsCount = 0;
    const [currentPage, setCurrentPage] = useState(1);

    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4'>
            <div className='flex items-center justify-between gap-2'>
                <h1 className='text-[48px] font-semibold'>Payment Record</h1>
                <div className='flex gap-4'>
                    <div className='min-w-[330px]'>
                        <TableSearchbar placeholder='Search' />
                    </div>
                    <Button className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                        Search
                    </Button>
                </div>
            </div>
            <div className='grid grid-rows-[1fr_auto] rounded-lg border bg-white shadow-lg'>
                <PaymentsTable />
                <div className='flex items-center justify-between p-4'>
                    <div className='text-[15px] font-medium text-black/50'>
                        Showing {recordsCount} record(s) of page {currentPage}
                    </div>
                    <Pagination
                        currentPage={currentPage}
                        numberOfPages={5}
                        onNextPage={() => {}}
                        onPrevPage={() => {}}
                        onPageChange={() => {}}
                    />
                    <div className='flex items-center gap-2 text-sm'>
                        <span>Page</span>
                        <Input
                            value={currentPage}
                            className='h-8 w-16 text-center'
                            type='number'
                            readOnly
                        />
                        <span>of {10}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}

Payments.layout = (page: ReactNode) => (
    <AdsPartnerLayout>{page}</AdsPartnerLayout>
);

export default Payments;
