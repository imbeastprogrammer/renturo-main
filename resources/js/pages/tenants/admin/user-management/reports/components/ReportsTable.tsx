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

import DeleteReportsModal from './DeleteReportsModal';

type ReportsTableProps = {
    reports: [];
};

function ReportsTable({ reports }: ReportsTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const navigateToViewPage = (id: number) =>
        router.visit(`/admin/user-management/reports/${id}`);

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow className='text-base font-normal text-[#2E3436]'>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Display Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Issue</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow className='text-base font-normal text-[#2E3436]/50'>
                        <TableCell className='font-medium'>123</TableCell>
                        <TableCell>Kamote Kid</TableCell>
                        <TableCell>kamotekid@gmail.com</TableCell>
                        <TableCell>Account Issue</TableCell>
                        <TableCell className='text-right'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='-translate-x-6'>
                                    <DropdownMenuItem
                                        onClick={() => navigateToViewPage(1)}
                                    >
                                        View
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        className='text-red-500'
                                        onClick={() =>
                                            setDeleteModalState({
                                                isOpen: true,
                                                id: 1,
                                            })
                                        }
                                    >
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <DeleteReportsModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default ReportsTable;
