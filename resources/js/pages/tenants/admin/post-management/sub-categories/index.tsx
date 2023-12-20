import { ReactNode, useState } from 'react';
import { router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { useSearchParams } from '@/hooks/useSearchParams';
import { FormattedSubCategory } from '@/types/categories';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import SubCategoriesTable from './components/SubCategoriesTable';
import AdminLayout from '@/layouts/AdminLayout';
import CreateSubCategoryModal from './components/CreateSubCategoryModal';
import Pagination from '@/components/tenant/Pagination';

interface SubCategoriesProps {
    sub_categories: PaginatedCategories;
}

interface PaginatedCategories {
    current_page: number;
    last_page: number;
    data: FormattedSubCategory[];
    next_page_url: string | null;
    prev_page_url: string | null;
}
function SubCategories({ sub_categories }: SubCategoriesProps) {
    const { pathname } = window.location;
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const { searchParams } = useSearchParams();
    const page = Number(searchParams.get('page')) || 1;

    const handleShowCreateCategoryModal = () => setShowCategoryModal(true);
    const handleNextPage = () =>
        sub_categories.next_page_url &&
        router.replace(sub_categories.next_page_url);

    const handlePrevPage = () => {
        sub_categories.prev_page_url &&
            router.replace(sub_categories.prev_page_url);
    };
    const handlePageChange = (page: number) =>
        router.replace(`${pathname}?page=${page}`);

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
                        Create New Category
                    </Button>
                </div>
                <SubCategoriesTable subCategories={sub_categories.data} />
                <div className='flex items-center justify-between'>
                    <div className='text-sm'>
                        <span>
                            Showing {sub_categories.data.length} Records of Page{' '}
                            {sub_categories.current_page}
                        </span>
                    </div>
                    <Pagination
                        currentPage={sub_categories.current_page}
                        numberOfPages={sub_categories.last_page}
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
            <CreateSubCategoryModal
                isOpen={showCategoryModal}
                onClose={() => setShowCategoryModal(false)}
            />
        </>
    );
}

SubCategories.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default SubCategories;
