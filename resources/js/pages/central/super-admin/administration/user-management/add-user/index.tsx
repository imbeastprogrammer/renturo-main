import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import AddUserForm from './components/AddUserForm';

function AddUser() {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='rounded-xl bg-white p-6 shadow-lg'>
                <AddUserForm />
            </div>
        </div>
    );
}

AddUser.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default AddUser;
