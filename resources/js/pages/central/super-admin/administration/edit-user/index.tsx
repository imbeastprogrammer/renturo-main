import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import EditUserForm from './components/EditUserForm';

function EditUser() {
    return (
        <div className='grid grid-rows-[80px_1fr] p-4'>
            <div></div>
            <div className='rounded-xl bg-white p-6 shadow-lg'>
                <EditUserForm />
            </div>
        </div>
    );
}

EditUser.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default EditUser;
