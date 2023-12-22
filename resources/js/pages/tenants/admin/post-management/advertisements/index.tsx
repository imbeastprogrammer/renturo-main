import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';

function Advertisements() {
    return (
        <div className='h-full rounded-lg border bg-white p-4 shadow-lg'>
            Ads
        </div>
    );
}

Advertisements.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Advertisements;
