import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';
import AdminLayout from '@/layouts/AdminLayout';
import CreateOwnerForm from './components/CreateOwnerForm';

function CreateOwner() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <CreateOwnerForm />
        </ScrollArea>
    );
}

CreateOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default CreateOwner;
