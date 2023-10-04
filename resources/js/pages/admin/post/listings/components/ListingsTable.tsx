import { useState } from "react";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { ListingStatusSelector } from "./ListingStatusSelector";

function ListingsTable() {
    const [status, setStatus] = useState<"posted" | "to review" | "declined">(
        "posted"
    );

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
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Dela Cruz Basketball Court</TableCell>
                    <TableCell>Joshua Dela Cruz</TableCell>
                    <TableCell>PHP 15k - 30k</TableCell>
                    <TableCell>
                        <ListingStatusSelector
                            value={status}
                            onChange={(value) => setStatus(value)}
                        />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}

export default ListingsTable;
