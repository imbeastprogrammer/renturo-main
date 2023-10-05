import { useState } from "react";
import { HomeIcon, TrashIcon } from "lucide-react";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { ListingStatusSelector } from "../../listings/components/ListingStatusSelector";

const statusColor: Record<string, string> = {
    approved: "#B1EEB7",
    "to review": "#FBDF88",
    declined: "#FFA1A1",
};

const statuses = [
    { label: "Approved", value: "approved" },
    { label: "To Review", value: "to review" },
    { label: "Declined", value: "declined" },
];

function CategoriesTable() {
    const [status, setStatus] = useState(
        statuses[Math.floor(Math.random() * 3)].value
    );

    const handleUpdateStatus = (status: string) => {
        //update status here
        setStatus(status);
    };

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[50px]">#</TableHead>
                    <TableHead>Id</TableHead>
                    <TableHead>Category Name</TableHead>
                    <TableHead>Icon</TableHead>
                    <TableHead>Parent</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="w-[50px]"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Business</TableCell>
                    <TableCell>
                        <HomeIcon />
                    </TableCell>
                    <TableCell>None</TableCell>
                    <TableCell>July 26, 2023</TableCell>
                    <TableCell>
                        <ListingStatusSelector
                            value={status}
                            data={statuses}
                            onChange={handleUpdateStatus}
                            color={statusColor[status]}
                        />
                    </TableCell>
                    <TableCell>
                        <TrashIcon className="text-red-500" />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}

export default CategoriesTable;
