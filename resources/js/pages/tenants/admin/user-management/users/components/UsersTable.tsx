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
import { User } from '@/types/users';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import DeleteUserModal from './DeleteUserModal';

type UsersTableProps = {
    users: User[];
};

function UsersTable({ users = [] }: UsersTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const navigateToUpdatePage = (id: number) => {
        router.visit(`/admin/user-management/users/update/${id}?active=Users`);
    };

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow className='text-base font-normal text-[#2E3436]'>
                        <TableHead className='w-[100px]'>Id</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Date Joined</TableHead>
                        <TableHead>Account</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='w-[50px]'></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {users.map((user) => (
                        <TableRow
                            key={user.id}
                            className='text-base font-normal text-[#2E3436]/50'
                        >
                            <TableCell className='font-medium'>
                                {user.id}
                            </TableCell>
                            <TableCell>
                                {[user.first_name, user.last_name].join(' ')}
                            </TableCell>
                            <TableCell>{user.email}</TableCell>
                            <TableCell>
                                {new Date(user.created_at).toDateString()}
                            </TableCell>
                            <TableCell>{user.role}</TableCell>
                            <TableCell>
                                {/* <span
                                className="capitalize"
                                style={{ color: statusColor[user.status] }}
                            >
                                {user.status}

                            </span> */}
                                NA for now
                            </TableCell>
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <MoreHorizontalIcon />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent className='-translate-x-6'>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                navigateToUpdatePage(user.id)
                                            }
                                        >
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            className='text-red-500'
                                            onClick={() =>
                                                setDeleteModalState({
                                                    isOpen: true,
                                                    id: user.id,
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
            <DeleteUserModal
                isOpen={deleteModalState.isOpen}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
                userToDeleteId={deleteModalState.id}
            />
        </>
    );
}

export default UsersTable;
