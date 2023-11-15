import SuperAdminLayout, { LabelMap } from '@/layouts/SuperAdminLayout';
import EditRoleForm from './components/EditRoleForm';
import { User } from '@/types/users';

type EditRoleProps = {
    user: User;
};
function EditRole({ user }: EditRoleProps) {
    return (
        <div className='h-full overflow-hidden p-4'>
            <div className='h-full rounded-xl bg-white p-6 shadow-lg'>
                <EditRoleForm />
            </div>
        </div>
    );
}

EditRole.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default EditRole;
