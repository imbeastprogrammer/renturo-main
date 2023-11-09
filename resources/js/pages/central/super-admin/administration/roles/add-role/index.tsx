import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import AddRoleForm from './components/AddRoleForm';

function AddRole() {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='rounded-xl bg-white p-6 shadow-lg'>
                <AddRoleForm />
            </div>
        </div>
    );
}

AddRole.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default AddRole;
