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
                    <TableHead>First Name</TableHead>
                    <TableHead>Last Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Domain</TableHead>
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
                        <TableCell>Static</TableCell>
                        <TableCell>Static</TableCell>
                        <TableCell>Static</TableCell>
                        <TableCell>Static</TableCell>
                        <TableCell className='text-right'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='-translate-x-4 font-outfit'>
                                    <DropdownMenuItem
                                        onClick={() =>
                                            navigateToEditPage(tenant.id)
                                        }
                                    >
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
