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
import { router } from '@inertiajs/react';

function OwnersTable() {
    const navigateToUpdatePage = (id: number) =>
        router.visit(`/admin/user-management/owners/update/${id}?active=Users`);

    return (
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
                <TableRow className='text-base font-normal text-[#2E3436]/50'>
                    <TableCell className='font-medium'>1</TableCell>
                    <TableCell>Joshua</TableCell>
                    <TableCell>Dela Cruz</TableCell>
                    <TableCell>kamotekid.dev@gmail.com</TableCell>
                    <TableCell>Active</TableCell>
                    <TableCell>10-27-23 16:50:32</TableCell>
                    <TableCell>10-27-23 16:50:32</TableCell>
                    <TableCell className='text-right'>
                        <DropdownMenu>
                            <DropdownMenuTrigger>
                                <MoreHorizontalIcon />
                            </DropdownMenuTrigger>
                            <DropdownMenuContent className='-translate-x-6'>
                                <DropdownMenuItem
                                    onClick={() => navigateToUpdatePage(1)}
                                >
                                    Edit
                                </DropdownMenuItem>
                                <DropdownMenuItem className='text-red-500'>
                                    Delete
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}

export default OwnersTable;
