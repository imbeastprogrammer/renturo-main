import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

const statusColor: Record<string, string> = {
    done: "#B1EEB7",
    upcoming: "#FBDF88",
    canceled: "#FFA1A1",
};

function BookingsTable() {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[50px]">#</TableHead>
                    <TableHead>Id</TableHead>
                    <TableHead>Listing Name</TableHead>
                    <TableHead>Booked By</TableHead>
                    <TableHead>Date Booked</TableHead>
                    <TableHead>Reservation Date</TableHead>
                    <TableHead>Price</TableHead>
                    <TableHead>Status</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Dela Cruz Basketball Court</TableCell>
                    <TableCell>Joshua Dela Cruz</TableCell>
                    <TableCell>July 15, 2023</TableCell>
                    <TableCell>July 26, 2023</TableCell>
                    <TableCell>₱ 15,000</TableCell>
                    <TableCell>
                        <Badge
                            className="text-black w-full justify-center"
                            style={{ background: statusColor["done"] }}
                        >
                            Done
                        </Badge>
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className="font-medium">2</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Dela Cruz Basketball Court</TableCell>
                    <TableCell>Joshua Dela Cruz</TableCell>
                    <TableCell>July 15, 2023</TableCell>
                    <TableCell>July 26, 2023</TableCell>
                    <TableCell>₱ 15,000</TableCell>
                    <TableCell>
                        <Badge
                            className="text-black w-full justify-center"
                            style={{ background: statusColor["upcoming"] }}
                        >
                            Upcoming
                        </Badge>
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className="font-medium">3</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Dela Cruz Basketball Court</TableCell>
                    <TableCell>Joshua Dela Cruz</TableCell>
                    <TableCell>July 15, 2023</TableCell>
                    <TableCell>July 26, 2023</TableCell>
                    <TableCell>₱ 15,000</TableCell>
                    <TableCell>
                        <Badge
                            className="text-black w-full justify-center"
                            style={{ background: statusColor["canceled"] }}
                        >
                            Canceled
                        </Badge>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}

export default BookingsTable;
