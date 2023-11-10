import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateAdminForm from './components/UpdateAdminForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function UpdateAdmin() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateAdminForm />
        </ScrollArea>
    );
}

UpdateAdmin.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateAdmin;
