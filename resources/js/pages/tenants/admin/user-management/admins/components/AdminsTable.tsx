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
import { MoreHorizontalIcon } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import { User } from '@/types/users';
import DeleteAdminModal from './DeleteAdminModal';
import formatDate from '@/lib/formatDate';

type AdminsTableProps = {
    admins: User[];
};

function AdminsTable({ admins }: AdminsTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const navigateToUpdatePage = (id: number) =>
        router.visit(`/admin/user-management/admins/update/${id}`);

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow className='text-base font-normal text-[#2E3436]'>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>First Name</TableHead>
                        <TableHead>Last Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Last Update</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {admins.map((admin) => (
                        <TableRow
                            key={admin.id}
                            className='text-base font-normal text-[#2E3436]/50'
                        >
                            <TableCell className='font-medium'>
                                {admin.id}
                            </TableCell>
                            <TableCell>{admin.first_name}</TableCell>
                            <TableCell>{admin.last_name}</TableCell>
                            <TableCell>{admin.email}</TableCell>
                            <TableCell>Status</TableCell>
                            <TableCell>
                                {formatDate(admin.created_at)}
                            </TableCell>
                            <TableCell>Static</TableCell>
                            <TableCell className='text-right'>
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <MoreHorizontalIcon />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent className='-translate-x-6'>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                navigateToUpdatePage(admin.id)
                                            }
                                        >
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            className='text-red-500'
                                            onClick={() =>
                                                setDeleteModalState({
                                                    isOpen: true,
                                                    id: admin.id,
                                                })
                                            }
                                        >
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <DeleteAdminModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default AdminsTable;
