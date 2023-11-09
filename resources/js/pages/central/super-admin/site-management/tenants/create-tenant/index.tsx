import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import CreateTenantForm from './components/CreateTenantForm';
import { ScrollArea } from '@/components/ui/scroll-area';

function CreateTenant() {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <ScrollArea className='rounded-xl bg-white shadow-lg'>
                <CreateTenantForm />
            </ScrollArea>
        </div>
    );
}

CreateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default CreateTenant;
