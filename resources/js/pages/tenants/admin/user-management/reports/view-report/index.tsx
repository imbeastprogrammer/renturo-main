import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';

import AdminLayout from '@/layouts/AdminLayout';
import Detail from './components/Detail';
import ImageList from './components/ImageList';

function ViewReport() {
    return (
        <ScrollArea className='rounded-lg border bg-white shadow-lg'>
            <div className='space-y-8 p-4'>
                <div className='space-y-4'>
                    <h1 className='text-[24px] font-semibold'>User Details</h1>
                    <div className='grid grid-cols-2 gap-6'>
                        <div className='flex items-center gap-8'>
                            <h2 className='text-lg font-medium'>Report Id</h2>
                            <p className='text-base text-black/90'>234</p>
                        </div>
                        <div></div>
                        <Detail label='Display Name'>Juana Hernandez</Detail>
                        <Detail label='Email'>email@yahoo.com</Detail>
                    </div>
                </div>
                <div className='space-y-4'>
                    <div>
                        <h1 className='text-[24px] font-semibold'>
                            Report Details
                        </h1>
                        <p className='text-base text-black/50'>
                            Preview the report details provided by the user.
                        </p>
                    </div>
                    <div className='grid grid-cols-2 gap-6'>
                        <Detail label='Issue'>Account Issue</Detail>
                        <Detail label='Email'>Mobile</Detail>
                        <Detail label='Status'>
                            Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Maecenas accumsan lacus vel
                            facilisis volutpat.
                        </Detail>
                    </div>
                </div>
                <ImageList />
            </div>
        </ScrollArea>
    );
}

ViewReport.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default ViewReport;
