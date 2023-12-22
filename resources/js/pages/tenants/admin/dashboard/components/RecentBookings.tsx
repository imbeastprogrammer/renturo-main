import { ChevronDown } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import dummyBookings from '@/data/dummyBookings';

const statusColor: Record<string, string> = {
    done: '#B1EEB7',
    upcoming: '#FBDF88',
    canceled: '#FFA1A1',
};

function RecentBookings() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4 overflow-x-auto rounded-lg border bg-white p-4 shadow-lg'>
            <h1 className='flex items-center gap-2 text-[22px] font-semibold leading-none'>
                Recent Bookings <ChevronDown />
            </h1>
            <RecentBookingsTable />
        </div>
    );
}

function RecentBookingsTable() {
    return (
        <Table className='w-max overflow-x-auto'>
            <TableHeader className='sticky top-0 bg-white'>
                <TableRow className='text-sm'>
                    <TableHead className='w-[50px]'>Id</TableHead>
                    <TableHead>Listing Name</TableHead>
                    <TableHead>Booked By</TableHead>
                    <TableHead>Booked Date</TableHead>
                    <TableHead>Reseveration Date</TableHead>
                    <TableHead>Price</TableHead>
                    <TableHead>Status</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {dummyBookings.map((booking) => (
                    <TableRow key={booking.id} className='text-[12px]'>
                        <TableCell className='font-medium'>
                            {booking.id}
                        </TableCell>
                        <TableCell>{booking.listing_name}</TableCell>
                        <TableCell>{booking.booked_by}</TableCell>
                        <TableCell>{booking.date_booked}</TableCell>
                        <TableCell>{booking.reservation_data}</TableCell>
                        <TableCell>{booking.price}</TableCell>
                        <TableCell>
                            <Badge
                                className='w-full justify-center text-[12px] uppercase text-black'
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

export default RecentBookings;
