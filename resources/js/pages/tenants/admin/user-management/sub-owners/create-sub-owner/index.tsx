import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import CreateSubOwnerForm from './components/CreateSubOwnerForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreateSubOwner() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <CreateSubOwnerForm />
        </ScrollArea>
    );
}

CreateSubOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default CreateSubOwner;
