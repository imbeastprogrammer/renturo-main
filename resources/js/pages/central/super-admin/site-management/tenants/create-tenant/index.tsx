import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import CreateTenantForm from './components/CreateTenantForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreateTenant() {
    return (
        <div className='h-full overflow-hidden p-4'>
            <ScrollArea className='h-full rounded-xl bg-white shadow-lg'>
                <CreateTenantForm />
            </ScrollArea>
        </div>
    );
}

CreateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default CreateTenant;
