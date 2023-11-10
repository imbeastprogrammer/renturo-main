import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import CreateAdminForm from './components/CreateAdminForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreateAdmin() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <CreateAdminForm />
        </ScrollArea>
    );
}

CreateAdmin.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default CreateAdmin;
