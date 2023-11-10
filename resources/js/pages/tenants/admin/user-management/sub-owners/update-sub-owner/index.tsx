import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateSubOwnerForm from './components/UpdateSubOwnerForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function UpdateSubOwner() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateSubOwnerForm />
        </ScrollArea>
    );
}

UpdateSubOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateSubOwner;
