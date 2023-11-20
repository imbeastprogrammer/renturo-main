import _ from 'lodash';
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
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreHorizontalIcon } from 'lucide-react';

import { Listing } from '@/types/listings';
import { ListingStatusSelector } from './ListingStatusSelector';
import ViewListingModal from './ViewListingModal';
import useOwnerToast from '@/hooks/useOwnerToast';

const statusColor: Record<string, string> = {
    approved: '#B1EEB7',
    pending: '#FFD555',
    declined: '#FFA1A1',
};

const statuses = [
    { label: 'Approved', value: 'approved' },
    { label: 'Pending', value: 'pending' },
    { label: 'Declined', value: 'declined' },
];

type ListingTableProps = {
    listings: Listing[];
};

function ListingsTable({ listings = [] }: ListingTableProps) {
    const toast = useOwnerToast();
    const [viewModalState, setViewModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const handleStatusUpdate = (value: string, id: number) => {
        router.put(
            `/admin/posts/${id}`,
            { status: value },
            {
                onSuccess: () =>
                    toast.success({ description: 'Listing status updated' }),
                onError: (errors) =>
                    toast.error({ description: _.valuesIn(errors)[0] }),
            },
        );
    };

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow className='text-base font-semibold text-black/50'>
                        <TableHead>Id</TableHead>
                        <TableHead>Title</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='text-center'>Action</TableHead>
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
                            <TableCell>{listing.description}</TableCell>
                            <TableCell>
                                <ListingStatusSelector
                                    value={listing.status}
                                    data={statuses}
                                    color={statusColor[listing.status]}
                                    onChange={(value) =>
                                        handleStatusUpdate(value, listing.id)
                                    }
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
                                                    id: listing.id,
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
                listings={listings}
                isOpen={viewModalState.isOpen}
                id={viewModalState.id}
                onClose={() => setViewModalState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default ListingsTable;
