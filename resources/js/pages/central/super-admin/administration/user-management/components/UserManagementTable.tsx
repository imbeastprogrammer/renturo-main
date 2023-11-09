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
import DeleteUserModal from './DeleteUserModal';

function UserManagementTable() {
    const [deleteModalState, setDeleteModalState] = useState({
        id: 0,
        isOpen: false,
    });

    const openDeleteUserModal = (id: number) =>
        setDeleteModalState({ id, isOpen: true });

    const navigateToEditPage = (id: number) =>
        router.visit(`/super-admin/administration/edit-user/${id}`);

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow className='text-base text-[#2E3436]'>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>First Name</TableHead>
                        <TableHead>Last Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Last Update</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow className='text-base text-[#2E3436]/50'>
                        <TableCell className='font-medium'>INV001</TableCell>
                        <TableCell>Joshua</TableCell>
                        <TableCell>Dela Cruz</TableCell>
                        <TableCell>kamotekiddev@gmail.com</TableCell>
                        <TableCell>Primary Super Admin</TableCell>
                        <TableCell>Active</TableCell>
                        <TableCell>10-27-23 16:50:32</TableCell>
                        <TableCell>10-27-23 16:50:32</TableCell>
                        <TableCell>
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
            <DeleteUserModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ id: 0, isOpen: false })}
            />
        </>
    );
}

export default UserManagementTable;
