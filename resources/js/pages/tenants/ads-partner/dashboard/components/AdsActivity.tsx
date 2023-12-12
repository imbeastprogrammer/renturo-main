import { FaArrowRight } from 'react-icons/fa';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { MoreHorizontal } from 'lucide-react';

const StatusMap = {
    active: <div className='h-[10px] w-[10px] rounded-full bg-green-500'></div>,
    inactive: <div className='h-[10px] w-[10px] rounded-full bg-red-500'></div>,
};

function AdsActivity() {
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
                        <TableHead>Ad Name</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Date Posted</TableHead>
                        <TableHead className='text-right'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow>
                        <TableCell className='font-medium'>1</TableCell>
                        <TableCell>11.11 Sale sa Renturo</TableCell>
                        <TableCell>
                            <div className='flex items-center gap-2'>
                                {StatusMap['active']} Active
                            </div>
                        </TableCell>
                        <TableCell>11-01-23</TableCell>
                        <TableCell className='text-right'>
                            <button>
                                <MoreHorizontal />
                            </button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    );
}

export default AdsActivity;
