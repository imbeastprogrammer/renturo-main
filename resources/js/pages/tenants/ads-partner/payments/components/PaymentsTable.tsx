import { useState } from 'react';
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
import ViewReceiptModal from './ViewReceiptModal';

function PaymentsTable() {
    const [viewReceiptState, setViewReceiptState] = useState({
        isOpen: false,
        id: 0,
    });

    return (
        <>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Purpose</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead>Transaction Date</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Payment Method</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow>
                        <TableCell>1</TableCell>
                        <TableCell>11.11 Ad</TableCell>
                        <TableCell>2,500.00 Php</TableCell>
                        <TableCell>11-01-23</TableCell>
                        <TableCell>Paid</TableCell>
                        <TableCell>Bank Transfer</TableCell>
                        <TableCell className='text-right'>
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <MoreHorizontalIcon />
                                </DropdownMenuTrigger>
                                <DropdownMenuContent className='-translate-x-6'>
                                    <DropdownMenuItem
                                        onClick={() =>
                                            setViewReceiptState({
                                                id: 1,
                                                isOpen: true,
                                            })
                                        }
                                    >
                                        View Receipt
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <ViewReceiptModal
                isOpen={viewReceiptState.isOpen}
                id={viewReceiptState.id}
                onClose={() => setViewReceiptState({ isOpen: false, id: 0 })}
            />
        </>
    );
}

export default PaymentsTable;
