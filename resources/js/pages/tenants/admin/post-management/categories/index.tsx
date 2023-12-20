import { ReactNode } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import TableSearchbar from '@/components/tenant/TableSearchbar';
import CategoriesTable from './components/CategoriesTable';
import AdminLayout from '@/layouts/AdminLayout';
import CreateCategoryModal from './components/CreateCategoryModal';

function Categories() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-y-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-center justify-between gap-2'>
                <div className='flex gap-4'>
                    <div className='min-w-[330px]'>
                        <TableSearchbar placeholder='Search' />
                    </div>
                    <Button className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                        Search
                    </Button>
                </div>
                <Button
                    type='button'
                    variant='outline'
                    className='items-center gap-2 border-metalic-blue text-[15px] font-medium text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
                >
                    <PlusIcon className='h-4 w-4' />
                    Create New Category
                </Button>
            </div>
            <CategoriesTable />
            <CreateCategoryModal isOpen onClose={() => {}} />
        </div>
    );
}

Categories.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Categories;
