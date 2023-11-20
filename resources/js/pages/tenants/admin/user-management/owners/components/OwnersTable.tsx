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
import DeleteOwnerModal from './DeleteOwnerModal';

type OwnerTableProps = {
    owners: User[];
};

function OwnersTable({ owners }: OwnerTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const navigateToUpdatePage = (id: number) =>
        router.visit(`/admin/user-management/owners/update/${id}?active=Users`);

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
                    {owners.map((owner) => (
                        <TableRow
                            key={owner.id}
                            className='text-base font-normal text-[#2E3436]/50'
                        >
                            <TableCell className='font-medium'>
                                {owner.id}
                            </TableCell>
                            <TableCell>{owner.first_name}</TableCell>
                            <TableCell>{owner.last_name}</TableCell>
                            <TableCell>{owner.email}</TableCell>
                            <TableCell>Static</TableCell>
                            <TableCell>{owner.created_at}</TableCell>
                            <TableCell>Static</TableCell>
                            <TableCell className='text-right'>
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <MoreHorizontalIcon />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent className='-translate-x-6'>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                navigateToUpdatePage(owner.id)
                                            }
                                        >
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            className='text-red-500'
                                            onClick={() =>
                                                setDeleteModalState({
                                                    isOpen: true,
                                                    id: owner.id,
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
            <DeleteOwnerModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default OwnersTable;
