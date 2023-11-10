import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateUserForm from './components/UpdateUserForm';

type UpdateUserProps = { user: User };
function UpdateUser({ user }: UpdateUserProps) {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateUserForm user={user} />
        </ScrollArea>
    );
}

UpdateUser.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateUser;
