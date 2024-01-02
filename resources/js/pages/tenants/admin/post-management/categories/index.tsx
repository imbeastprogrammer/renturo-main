import { ReactNode, useState } from 'react';
import { router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { useSearchParams } from '@/hooks/useSearchParams';
import { Category } from '@/types/categories';
import TableSearchbar from '@/components/tenant/TableSearchbar';
import CategoriesTable from './components/CategoriesTable';
import AdminLayout from '@/layouts/AdminLayout';
import CreateCategoryModal from './components/CreateCategoryModal';
import Pagination from '@/components/tenant/Pagination';

interface CategoriesProps {
    categories: PaginatedCategories;
}

interface PaginatedCategories {
    current_page: number;
    last_page: number;
    data: Category[];
    next_page_url: string | null;
    prev_page_url: string | null;
}
function Categories({ categories }: CategoriesProps) {
    const { pathname } = window.location;
    const [showCategoryModal, setShowCategoryModal] = useState(false);
    const { searchParams } = useSearchParams();
    const page = Number(searchParams.get('page')) || 1;

    const handleShowCreateCategoryModal = () => setShowCategoryModal(true);
    const handleNextPage = () =>
        categories.next_page_url && router.replace(categories.next_page_url);

    const handlePrevPage = () => {
        categories.prev_page_url && router.replace(categories.prev_page_url);
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
                <CategoriesTable categories={categories.data} />
                <div className='flex items-center justify-between'>
                    <div className='text-sm'>
                        <span>
                            Showing {categories.data.length} Records of Page{' '}
                            {categories.current_page}
                        </span>
                    </div>
                    <Pagination
                        currentPage={categories.current_page}
                        numberOfPages={categories.last_page}
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
            <CreateCategoryModal
                isOpen={showCategoryModal}
                onClose={() => setShowCategoryModal(false)}
            />
        </>
    );
}

Categories.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default Categories;
