import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import CreateTenantForm from './components/CreateTenantForm';

function CreateTenant() {
    return (
        <div className='grid grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='overflow-auto rounded-xl bg-white p-6 shadow-lg'>
                <CreateTenantForm />
            </div>
        </div>
    );
}

CreateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default CreateTenant;
