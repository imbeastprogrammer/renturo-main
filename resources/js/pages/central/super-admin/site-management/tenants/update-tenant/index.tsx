import { ScrollArea } from '@/components/ui/scroll-area';
import { Tenant } from '@/types/tenant';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import UpdateTeanantForm from './components/UpdateTenantForm';

type UpdateTenantProps = { tenant: Tenant };
function UpdateTenant({ tenant }: UpdateTenantProps) {
    return (
        <div className='h-full overflow-hidden p-4'>
            <ScrollArea className='h-full rounded-xl bg-white shadow-lg'>
                <UpdateTeanantForm tenant={tenant} />
            </ScrollArea>
        </div>
    );
}

UpdateTenant.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default UpdateTenant;
