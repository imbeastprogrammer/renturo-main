import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateOwnerFOrm from './components/UpdateOwnerForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function UpdateOwner() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateOwnerFOrm />
        </ScrollArea>
    );
}

UpdateOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateOwner;
