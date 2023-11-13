import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Booking } from "@/types/bookings";

const statusColor: Record<string, string> = {
    done: "#B1EEB7",
    upcoming: "#FBDF88",
    canceled: "#FFA1A1",
};

type BookingsTableProps = {
    bookings: Booking[];
};

function BookingsTable({ bookings = [] }: BookingsTableProps) {
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
                {bookings.map((booking) => (
                    <TableRow key={booking.no}>
                        <TableCell className="font-medium">
                            {booking.no}
                        </TableCell>
                        <TableCell>{booking.id}</TableCell>
                        <TableCell>{booking.listing_name}</TableCell>
                        <TableCell>{booking.booked_by}</TableCell>
                        <TableCell>{booking.date_booked}</TableCell>
                        <TableCell>{booking.reservation_data}</TableCell>
                        <TableCell>{booking.price}</TableCell>
                        <TableCell>
                            <Badge
                                className="text-black uppercase w-full justify-center"
                                style={{
                                    background: statusColor[booking.status],
                                }}
                            >
                                {booking.status}
                            </Badge>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default BookingsTable;
