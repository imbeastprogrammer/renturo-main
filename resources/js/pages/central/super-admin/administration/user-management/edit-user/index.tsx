import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import EditUserForm from './components/EditUserForm';

import { User } from '@/types/users';

type EditUserProps = { user: User };
function EditUser({ user }: EditUserProps) {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='rounded-xl bg-white p-6 shadow-lg'>
                <EditUserForm user={user} />
            </div>
        </div>
    );
}

EditUser.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default EditUser;
