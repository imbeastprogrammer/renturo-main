import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import EditRoleForm from './components/EditRoleForm';

function EditRole() {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='rounded-xl bg-white p-6 shadow-lg'>
                <EditRoleForm />
            </div>
        </div>
    );
}

EditRole.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default EditRole;
