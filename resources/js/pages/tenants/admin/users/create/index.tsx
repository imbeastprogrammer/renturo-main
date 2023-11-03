import AdminLayout from '@/layouts/AdminLayout';
import CreateUserForm from './CreateUserForm';

type CreateUserPageProps = {
    errors: { email: string };
    flash: { success: string };
};
function CreateUserPage(props: CreateUserPageProps) {
    return (
        <div className='grid h-full grid-rows-[auto_auto_1fr] gap-y-4 rounded-lg border p-8 shadow-lg'>
            <p className='text-[15px] font-medium text-gray-500'>
                Users / User Management / Add User
            </p>
            <h1 className='text-[30px] font-semibold leading-none'>Add User</h1>
            <CreateUserForm />
        </div>
    );
}

CreateUserPage.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default CreateUserPage;
