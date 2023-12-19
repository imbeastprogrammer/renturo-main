import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { NotDataFoundHero } from '@/assets/tenant/owner/promotions';

function CategoriesTable() {
    return <NoDataFound />;

    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className='w-[100px]'>ID</TableHead>
                    <TableHead>Category Name</TableHead>
                    <TableHead>Icon</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Action</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody></TableBody>
        </Table>
    );
}

function NoDataFound() {
    return (
        <div className='grid grid-rows-[auto_1fr]'>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Category Name</TableHead>
                        <TableHead>Icon</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody></TableBody>
            </Table>
            <div className='grid place-items-center p-4'>
                <div className='space-y-8 text-center'>
                    <img
                        src={NotDataFoundHero}
                        alt='No Data Found Hero Image'
                        className='mx-auto'
                    />
                    <h1 className='text-[32px] font-semibold text-metalic-blue'>
                        No Category? No problem!
                    </h1>
                    <p className='text-xl'>
                        Click the{' '}
                        <span className='text-metalic-blue'>
                            “+ Create New Category”
                        </span>{' '}
                        or the{' '}
                        <span className='text-metalic-blue'>“Get Started”</span>{' '}
                        button below to get <br /> your business noticed.
                    </p>
                    <Button className='h-[40px] w-[136px] bg-metalic-blue font-medium hover:bg-metalic-blue/90'>
                        Get Started
                    </Button>
                </div>
            </div>
        </div>
    );
}

export default CategoriesTable;
