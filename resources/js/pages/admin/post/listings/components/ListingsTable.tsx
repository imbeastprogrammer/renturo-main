import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { ListingStatusSelector } from "./ListingStatusSelector";
import { Listing } from "@/types/listings";

const statusColor: Record<string, string> = {
    posted: "#B1EEB7",
    "to review": "#FBDF88",
    declined: "#FFA1A1",
};

const statuses = [
    { label: "Posted", value: "posted" },
    { label: "To Review", value: "to review" },
    { label: "Declined", value: "declined" },
];

type ListingTableProps = {
    listings: Listing[];
};

function ListingsTable({ listings = [] }: ListingTableProps) {
    const handleStatusUpdate = (value: string) => {
        // update status here
    };

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[100px]">#</TableHead>
                    <TableHead>Id</TableHead>
                    <TableHead>Listing Name</TableHead>
                    <TableHead>Posted By</TableHead>
                    <TableHead>Price Range</TableHead>
                    <TableHead>Status</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {listings.map((listing) => (
                    <TableRow>
                        <TableCell className="font-medium">
                            {listing.no}
                        </TableCell>
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
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default ListingsTable;
