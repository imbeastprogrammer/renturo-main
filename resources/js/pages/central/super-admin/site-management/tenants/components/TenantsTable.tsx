import { useState } from 'react';
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
import DeleteTenantsModal from './DeleteTenantsModal';

type TenantsTableProps = {
    tenants: Tenant[];
};

function TenantsTable({ tenants }: TenantsTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        id: '',
        isOpen: false,
    });

    const openDeleteUserModal = (id: string) =>
        setDeleteModalState({ id, isOpen: true });

    const navigateToEditPage = (id: string) =>
        router.visit(`/super-admin/site-mangement/tenants/edit/${id}`);

    return (
        <>
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
                            <TableCell>tenant</TableCell>
                            <TableCell>tenant</TableCell>
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
                                        <DropdownMenuItem
                                            onClick={() =>
                                                openDeleteUserModal(tenant.id)
                                            }
                                        >
                                            Delete Tenant
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <DeleteTenantsModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ id: '', isOpen: false })}
            />
        </>
    );
}

export default TenantsTable;
