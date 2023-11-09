import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import UpdateTeanantForm from './components/UpdateTenantForm';

function UpdateTenant() {
    return (
        <div className='grid grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <div className='overflow-auto rounded-xl bg-white p-6 shadow-lg'>
                <UpdateTeanantForm />
            </div>
        </div>
    );
}

UpdateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default UpdateTenant;
