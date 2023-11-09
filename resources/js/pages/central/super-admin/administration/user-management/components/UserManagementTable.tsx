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

import { User } from '@/types/users';
import DeleteUserModal from './DeleteUserModal';

type UserMangementTableProps = { users: User[] };

function UserManagementTable({ users }: UserMangementTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        id: 0,
        isOpen: false,
    });

    const openDeleteUserModal = (id: number) =>
        setDeleteModalState({ id, isOpen: true });

    const navigateToEditPage = (id: number) =>
        router.visit(`/super-admin/administration/user-management/edit/${id}`);

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
                    {users.map((user) => (
                        <TableRow
                            key={user.id}
                            className='text-base text-[#2E3436]/50'
                        >
                            <TableCell className='font-medium'>
                                {user.id}
                            </TableCell>
                            <TableCell>{user.first_name}</TableCell>
                            <TableCell>{user.last_name}</TableCell>
                            <TableCell>{user.email}</TableCell>
                            <TableCell>{user.role}</TableCell>
                            <TableCell>Active (static)</TableCell>
                            <TableCell>{user.created_at}</TableCell>
                            <TableCell>10-27-23 16:50:32 (static)</TableCell>
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <MoreHorizontalIcon />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent className='font-outfit'>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                navigateToEditPage(user.id)
                                            }
                                        >
                                            Edit User
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                openDeleteUserModal(user.id)
                                            }
                                        >
                                            Delete User
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
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
