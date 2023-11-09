import { Tenant } from '@/types/tenant';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import UpdateTeanantForm from './components/UpdateTenantForm';
import { ScrollArea } from '@/components/ui/scroll-area';

type UpdateTenantProps = { tenant: Tenant };
function UpdateTenant({ tenant }: UpdateTenantProps) {
    return (
        <div className='grid h-full grid-rows-[80px_1fr] overflow-hidden p-4'>
            <div></div>
            <ScrollArea className='rounded-xl bg-white shadow-lg'>
                <UpdateTeanantForm tenant={tenant} />
            </ScrollArea>
        </div>
    );
}

UpdateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default UpdateTenant;
