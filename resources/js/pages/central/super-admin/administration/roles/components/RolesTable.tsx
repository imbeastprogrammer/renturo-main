import { useState } from 'react';
import { router } from '@inertiajs/react';
import { FiEdit3 } from 'react-icons/fi';
import { RxTrash } from 'react-icons/rx';
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
                                <DropdownMenuContent className='-translate-x-6 font-outfit'>
                                    <DropdownMenuItem
                                        className='gap-2'
                                        onClick={() => navigateToEditPage(1)}
                                    >
                                        <FiEdit3 className='h-[22px] w-[22px] text-base' />
                                        Edit User
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        className='gap-2 text-red-500 focus:text-red-500'
                                        onClick={() => openDeleteUserModal(1)}
                                    >
                                        <RxTrash className='h-[22px] w-[22px] text-base' />{' '}
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
