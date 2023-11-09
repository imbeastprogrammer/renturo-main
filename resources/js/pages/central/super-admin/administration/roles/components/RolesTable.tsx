import { useState } from 'react';
import { router } from '@inertiajs/react';
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
import { MoreHorizontalIcon } from 'lucide-react';
import DeleteRolesModal from './DeleteRolesModal';

function RolesTable() {
    const [deleteModalState, setDeleteModalState] = useState({
        id: 0,
        isOpen: false,
    });

    const openDeleteUserModal = (id: number) =>
        setDeleteModalState({ id, isOpen: true });

    const navigateToEditPage = (id: number) =>
        router.visit(`/super-admin/administration/roles/edit/${id}`);

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow className='text-base text-[#2E3436]'>
                        <TableHead>ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Persmission Type</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow className='text-base text-[#2E3436]/50'>
                        <TableCell className='font-medium'>INV001</TableCell>
                        <TableCell>Primary Super Admin</TableCell>
                        <TableCell>All</TableCell>
                        <TableCell className='text-right'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='font-outfit'>
                                    <DropdownMenuItem
                                        onClick={() => navigateToEditPage(1)}
                                    >
                                        Edit User
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        onClick={() => openDeleteUserModal(1)}
                                    >
                                        Delete User
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <DeleteRolesModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ id: 0, isOpen: false })}
            />
        </>
    );
}

export default RolesTable;
