import { ReactNode } from 'react';
import { ScrollArea } from '@/components/ui/scroll-area';

import { User } from '@/types/users';
import AdminLayout from '@/layouts/AdminLayout';
import UpdateOwnerForm from './components/UpdateOwnerForm';

type UpdateOwnerProps = {
    owner: User;
};
function UpdateOwner({ owner }: UpdateOwnerProps) {
    return (
        <ScrollArea className='rounded-xl border bg-white shadow-lg'>
            <UpdateOwnerForm owner={owner} />
        </ScrollArea>
    );
}

UpdateOwner.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default UpdateOwner;
