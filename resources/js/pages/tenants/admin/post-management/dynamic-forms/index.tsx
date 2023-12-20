import { ReactNode, useState } from 'react';
import { router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { useSearchParams } from '@/hooks/useSearchParams';
import { Category } from '@/types/categories';
import { DynamicForm } from '@/types/dynamic-form';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import DynamicFormsTable from './components/DynamicFormsTable';
import AdminLayout from '@/layouts/AdminLayout';
import CreateDynamicFormModal from './components/CreateCategoryModal';
import Pagination from '@/components/tenant/Pagination';

interface DynamicFormsProps {
    dynamic_forms: PaginatedDynamicForms;
}

interface PaginatedDynamicForms {
    data: DynamicForm[];
    pagination: {
        total: number;
        perPage: number;
        currentPage: number;
        lastPage: number;
    };
}
function DynamicForms({ dynamic_forms }: DynamicFormsProps) {
    const { pathname } = window.location;
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const { searchParams } = useSearchParams();
    const page = Number(searchParams.get('page')) || 1;

    const handleShowCreateCategoryModal = () => setShowCategoryModal(true);
    const handleNextPage = (newPage: number) =>
        router.replace(pathname, { data: { page: newPage } });

    const handlePrevPage = (newPage: number) => {
        router.replace(pathname, { data: { page: newPage } });
    };

    const handlePageChange = (newPage: number) =>
        router.replace(pathname, { data: { page: newPage } });

    return (
        <>
            <div className='grid h-full grid-rows-[auto_1fr_auto] gap-y-4 overflow-hidden rounded-lg border bg-white p-4 shadow-lg'>
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
                        onClick={handleShowCreateCategoryModal}
                    >
                        <PlusIcon className='h-4 w-4' />
                        Create New Form
                    </Button>
                </div>
                <DynamicFormsTable dynamicForms={dynamic_forms.data} />
                <div className='flex items-center justify-between'>
                    <div className='text-sm'>
                        <span>
                            Showing {dynamic_forms.data.length} Records of Page{' '}
                            {dynamic_forms.pagination.currentPage}
                        </span>
                    </div>
                    <Pagination
                        currentPage={dynamic_forms.pagination.currentPage}
                        numberOfPages={dynamic_forms.pagination.lastPage}
                        onNextPage={handleNextPage}
                        onPrevPage={handlePrevPage}
                        onPageChange={handlePageChange}
                    />
                    <div className='flex items-center gap-2 text-sm'>
                        <span>Page</span>
                        <Input
                            value={page}
                            className='h-8 w-16 text-center'
                            type='number'
                            readOnly
                        />
                        <span>of {100}</span>
                    </div>
                </div>
            </div>
            <CreateDynamicFormModal
                isOpen={showCategoryModal}
                onClose={() => setShowCategoryModal(false)}
            />
        </>
    );
}

DynamicForms.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default DynamicForms;
