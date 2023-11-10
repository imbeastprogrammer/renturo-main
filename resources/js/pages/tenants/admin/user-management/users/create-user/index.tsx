import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';
import AdminLayout from '@/layouts/AdminLayout';
import CreateUserForm from './components/CreateUserForm';

function CreateUser() {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <CreateUserForm />
        </ScrollArea>
    );
}

CreateUser.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default CreateUser;
