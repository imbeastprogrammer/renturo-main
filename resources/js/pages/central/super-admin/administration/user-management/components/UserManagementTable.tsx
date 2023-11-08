import { useState } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { MoreHorizontalIcon } from 'lucide-react';
import DeleteUserModal from './DeleteUserModal';

function UserManagementTable() {
    const [deleteModalState, setDeleteModalState] = useState({
        id: 0,
        isOpen: true,
    });
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
                            <MoreHorizontalIcon />
                        </TableCell>
                    </TableRow>
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
                            <MoreHorizontalIcon />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <DeleteUserModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() =>
                    setDeleteModalState((prev) => ({ id: 0, isOpen: false }))
                }
            />
        </>
    );
}

export default UserManagementTable;
