import { router } from '@inertiajs/react';
import { MoreHorizontalIcon } from 'lucide-react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import { Tenant } from '@/types/tenant';
import { FiEdit3 } from 'react-icons/fi';

type TenantsTableProps = {
    tenants: Tenant[];
};

function TenantsTable({ tenants }: TenantsTableProps) {
    const navigateToEditPage = (id: string) =>
        router.visit(`/super-admin/site-management/tenants/edit/${id}`);

    return (
        <Table>
            <TableHeader>
                <TableRow className='text-base text-[#2E3436]'>
                    <TableHead>ID</TableHead>
                    <TableHead>Company</TableHead>
                    <TableHead>Plan Type</TableHead>
                    <TableHead>Domain</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Created At</TableHead>
                    <TableHead>Updated At</TableHead>
                    <TableHead className='text-right'>Action</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {tenants.map((tenant) => (
                    <TableRow
                        key={tenant.id}
                        className='text-base text-[#2E3436]/50'
                    >
                        <TableCell className='font-medium'>
                            {tenant.id}
                        </TableCell>
                        <TableCell>{tenant.company}</TableCell>
                        <TableCell>{tenant.plan_type}</TableCell>
                        <TableCell>static</TableCell>
                        <TableCell>{tenant.status}</TableCell>
                        <TableCell>{tenant.created_at}</TableCell>
                        <TableCell>{tenant.updated_at || 'NA'}</TableCell>
                        <TableCell className='text-right'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='-translate-x-6 font-outfit'>
                                    <DropdownMenuItem
                                        className='gap-2'
                                        onClick={() =>
                                            navigateToEditPage(tenant.id)
                                        }
                                    >
                                        <FiEdit3 className='h-[22px] w-[22px] text-base' />
                                        Edit Tenant
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default TenantsTable;
