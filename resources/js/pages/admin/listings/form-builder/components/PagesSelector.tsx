import { FileIcon, GripVerticalIcon, TrashIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import useFormBuilder from '@/hooks/useFormBuilder';

function PagesSelector() {
    const { pages, setPage, current_page_id, removePage } = useFormBuilder();

    const handlePageChange = (page: string) => setPage(page);
    const handleRemovePage = (page: string) => removePage(page);

    return (
        <div className='space-y-2'>
            {pages.map((currentPage, i) => (
                <PageItem
                    key={currentPage.page_id}
                    page={currentPage.page_title}
                    number={i + 1}
                    isActive={currentPage.page_id === current_page_id}
                    onPageChange={handlePageChange}
                    onRemovePage={handleRemovePage}
                />
            ))}
            <div className='flex justify-end'>
                <Button className='mt-4 gap-2 bg-metalic-blue hover:bg-metalic-blue/90'>
                    <FileIcon className='h-4 w-4' />
                    Add Page
                </Button>
            </div>
        </div>
    );
}

type PageItemProps = {
    page: string;
    number: number;
    isActive: boolean;
    onPageChange: (page: string) => void;
    onRemovePage: (page: string) => void;
};

function PageItem({
    number,
    page,
    onPageChange,
    onRemovePage,
    isActive,
}: PageItemProps) {
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
                    <span className='font-semibold'>{`Page ${number}:`}</span>
                    {page || 'Untitled'}
                </div>
                <div>
                    <TrashIcon
                        className='h-5 w-5 text-red-500'
                        onClick={(e) => {
                            e.stopPropagation();
                            onRemovePage(page);
                        }}
                    />
                </div>
            </div>
        </div>
    );
}

export default PagesSelector;
