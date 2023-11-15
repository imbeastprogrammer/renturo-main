import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import AddRoleForm from './components/AddRoleForm';

function AddRole() {
    return (
        <div className='h-full overflow-hidden p-4'>
            <div className='h-full rounded-xl bg-white p-6 shadow-lg'>
                <AddRoleForm />
            </div>
        </div>
    );
}

AddRole.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default AddRole;
