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

// const statusColor: Record<string, string> = {
//     active: "#84C58A",
//     offline: "#EB4F4F",
// };

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
    const [deleteModalOpen, setDeletModalOpen] = useState(false);

    const handleMenuSelection = (value: string) => {
        switch (value) {
            case MenuItems.DELETE:
                return setDeletModalOpen(true);
            case MenuItems.VIEW:
            case MenuItems.UPDATE:
                return router.visit(`/admin/users/view/${10}?active=Users`);
        }
    };

    return (
        <>
            <Table>
                <TableHeader>
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
                            <TableCell>NA for now</TableCell>
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
                                    onSelect={handleMenuSelection}
                                />
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <DeleteUserModal
                isOpen={deleteModalOpen}
                onClose={() => setDeletModalOpen(false)}
                userToDeleteId={1}
            />
        </>
    );
}

export default UsersTable;
