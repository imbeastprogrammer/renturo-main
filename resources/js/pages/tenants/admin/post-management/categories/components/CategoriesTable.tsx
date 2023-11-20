import { useState } from 'react';
import { HomeIcon, TrashIcon } from 'lucide-react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ListingStatusSelector } from '../../properties/components/ListingStatusSelector';
import { Category } from '@/types/categories';

const statusColor: Record<string, string> = {
    approved: '#B1EEB7',
    'to review': '#FBDF88',
    declined: '#FFA1A1',
};

const statuses = [
    { label: 'Approved', value: 'approved' },
    { label: 'To Review', value: 'to review' },
    { label: 'Declined', value: 'declined' },
];

type CategoriesTableProps = {
    categories: Category[];
};

function CategoriesTable({ categories = [] }: CategoriesTableProps) {
    const handleUpdateStatus = (status: string) => {};

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className='w-[50px]'>#</TableHead>
                    <TableHead>Id</TableHead>
                    <TableHead>Category Name</TableHead>
                    <TableHead>Icon</TableHead>
                    <TableHead>Parent</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className='w-[50px]'></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {categories.map((category) => (
                    <TableRow key={category.no}>
                        <TableCell className='font-medium'>
                            {category.no}
                        </TableCell>
                        <TableCell>{category.id}</TableCell>
                        <TableCell>{category.category_name}</TableCell>
                        <TableCell>
                            <HomeIcon />
                        </TableCell>
                        <TableCell>{category.parent}</TableCell>
                        <TableCell>
                            <ListingStatusSelector
                                value={category.status}
                                data={statuses}
                                onChange={handleUpdateStatus}
                                color={statusColor[category.status]}
                            />
                        </TableCell>
                        <TableCell>
                            <TrashIcon className='text-red-500' />
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default CategoriesTable;
