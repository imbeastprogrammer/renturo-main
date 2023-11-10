import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import CreateOwnerForm from './components/CreateOwnerForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreateOwner() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <CreateOwnerForm />
        </ScrollArea>
    );
}

CreateOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default CreateOwner;
