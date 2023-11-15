import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import AddUserForm from './components/AddUserForm';

function AddUser() {
    return (
        <div className='h-full overflow-hidden p-4'>
            <div className='h-full rounded-xl bg-white p-6 shadow-lg'>
                <AddUserForm />
            </div>
        </div>
    );
}

AddUser.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default AddUser;
