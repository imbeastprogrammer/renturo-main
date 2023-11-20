import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateAdminForm from './components/UpdateAdminForm';

type UpdateadminProps = {
    admin: User;
};
function UpdateAdmin({ admin }: UpdateadminProps) {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateAdminForm admin={admin} />
        </ScrollArea>
    );
}

UpdateAdmin.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateAdmin;
