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
import { EditIcon, EyeIcon, TrashIcon } from 'lucide-react';
import { User } from '@/types/users';
import ActionMenu from './ActionMenu';
import DeleteUserModal from './DeleteUserModal';

type UsersTableProps = {
    users: User[];
};

enum MenuItems {
    UPDATE = 'Update',
    VIEW = 'View',
    DELETE = 'Delete',
}

const menuItems = [
    { label: MenuItems.UPDATE, icon: EditIcon },
    { label: MenuItems.VIEW, icon: EyeIcon },
    { label: MenuItems.DELETE, icon: TrashIcon },
];

function UsersTable({ users = [] }: UsersTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const handleMenuSelection = (value: string, id: number) => {
        switch (value) {
            case MenuItems.DELETE:
                return setDeleteModalState({ isOpen: true, id });
            case MenuItems.VIEW:
            case MenuItems.UPDATE:
                return router.visit(`/admin/users/view/${id}?active=Users`);
        }
    };

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow>
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
                        <TableRow key={user.id}>
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
                                <ActionMenu
                                    menuItems={menuItems}
                                    onSelect={(value) =>
                                        handleMenuSelection(value, user.id)
                                    }
                                />
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
