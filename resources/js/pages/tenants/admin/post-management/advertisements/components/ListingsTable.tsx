import { router } from '@inertiajs/react';
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
import { ListingStatusSelector } from './ListingStatusSelector';
import { MoreHorizontalIcon } from 'lucide-react';

import { Listing } from '@/types/listings';

const statusColor: Record<string, string> = {
    posted: '#B1EEB7',
    'to review': '#FBDF88',
    declined: '#FFA1A1',
};

const statuses = [
    { label: 'Posted', value: 'posted' },
    { label: 'To Review', value: 'to review' },
    { label: 'Declined', value: 'declined' },
];

type ListingTableProps = {
    listings: Listing[];
};

function ListingsTable({ listings = [] }: ListingTableProps) {
    const handleStatusUpdate = (value: string) => {
        // update status here
    };

    const navigateToViewPage = (id: number) => {
        router.visit(`/admin/post-management/advertisements/${id}`);
    };

    return (
        <Table>
            <TableHeader>
                <TableRow className='text-base font-semibold text-black/50'>
                    <TableHead>Id</TableHead>
                    <TableHead>Listing Name</TableHead>
                    <TableHead>Posted By</TableHead>
                    <TableHead>Price Range</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Action</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {listings.map((listing) => (
                    <TableRow
                        key={listing.id}
                        className='text-sm font-normal text-black/50'
                    >
                        <TableCell>{listing.id}</TableCell>
                        <TableCell>{listing.title}</TableCell>
                        <TableCell>Static</TableCell>
                        <TableCell>Static</TableCell>
                        <TableCell>
                            <ListingStatusSelector
                                value={listing.status}
                                data={statuses}
                                color={statusColor[listing.status]}
                                onChange={handleStatusUpdate}
                            />
                        </TableCell>
                        <TableCell className='text-center'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='-translate-x-8'>
                                    <DropdownMenuItem
                                        onClick={() =>
                                            navigateToViewPage(listing.id)
                                        }
                                    >
                                        View Advertisement
                                    </DropdownMenuItem>
                                    <DropdownMenuItem className='text-red-500'>
                                        Delete Advertisement
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default ListingsTable;
