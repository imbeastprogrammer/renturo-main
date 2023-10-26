import useFormBuilder, { Page } from '@/hooks/useFormBuilder';
import { cn } from '@/lib/utils';
import { GripVerticalIcon, TrashIcon } from 'lucide-react';

function PagesSelector() {
    const { pages, setPage, current_page } = useFormBuilder();

    const handlePageChange = (page: Page) => setPage(page);

    return (
        <div className='space-y-2'>
            {pages.map((page) => (
                <PageItem
                    page={page}
                    isActive={page.number === current_page.number}
                    onPageChange={handlePageChange}
                />
            ))}
        </div>
    );
}

type PageItemProps = {
    page: Page;
    isActive: boolean;
    onPageChange: (page: Page) => void;
};

function PageItem({ page, onPageChange, isActive }: PageItemProps) {
    return (
        <div
            onClick={() => onPageChange(page)}
            className={cn(
                'cursor-pointer select-none rounded-lg bg-metalic-blue/5 px-4 py-3 ring-2 ring-transparent transition',
                isActive && ' ring-metalic-blue',
            )}
        >
            <div className='flex justify-between'>
                <div className='flex gap-2'>
                    <GripVerticalIcon className='text-gray-400' />
                    <span className='font-semibold'>{`Page ${page.number}:`}</span>
                    {page.title}
                </div>
                <div>
                    <TrashIcon className='h-5 w-5 text-red-500' />
                </div>
            </div>
        </div>
    );
}

export default PagesSelector;
