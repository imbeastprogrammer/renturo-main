import { useState } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ListingStatusSelector } from './ListingStatusSelector';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreHorizontalIcon } from 'lucide-react';
import { Listing } from '@/types/listings';
import ViewListingModal from './ViewListingModal';

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
    const [viewModalState, setViewModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const handleStatusUpdate = (value: string) => {
        // update status here
    };

    return (
        <>
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
                                    <DropdownMenuContent>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                setViewModalState({
                                                    isOpen: true,
                                                    id: listing.no,
                                                })
                                            }
                                        >
                                            View Listing
                                        </DropdownMenuItem>
                                        <DropdownMenuItem className='text-red-500'>
                                            Delete Listing
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <ViewListingModal
                isOpen={viewModalState.isOpen}
                id={viewModalState.id}
                onClose={() => setViewModalState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default ListingsTable;
