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
        router.visit(`/admin/post-management/promotions/${id}?active=Post`);
    };

    return (
        <Table>
            <TableHeader>
                <TableRow className='text-base font-semibold text-black/50'>
                    <TableHead className='w-[100px]'>#</TableHead>
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
                        key={listing.no}
                        className='text-sm font-normal text-black/50'
                    >
                        <TableCell>{listing.no}</TableCell>
                        <TableCell>{listing.id}</TableCell>
                        <TableCell>{listing.listing_name}</TableCell>
                        <TableCell>{listing.posted_by}</TableCell>
                        <TableCell>{listing.price_range}</TableCell>
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
                                            navigateToViewPage(listing.no)
                                        }
                                    >
                                        View Promotion
                                    </DropdownMenuItem>
                                    <DropdownMenuItem className='text-red-500'>
                                        Delete Promotion
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
