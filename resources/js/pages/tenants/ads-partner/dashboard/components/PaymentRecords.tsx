import { FaArrowRight } from 'react-icons/fa';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

function PaymentRecords() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-center justify-between gap-4'>
                <h1 className='text-lg font-medium'>Ads Activity</h1>
                <FaArrowRight />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Purpose</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead>Transaction Date</TableHead>
                        <TableHead>Status</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow>
                        <TableCell className='font-medium'>1</TableCell>
                        <TableCell>11.11 Ad</TableCell>
                        <TableCell>2,500 Php</TableCell>
                        <TableCell>11-01-23</TableCell>
                        <TableCell>Paid</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    );
}

export default PaymentRecords;
