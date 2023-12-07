import { DragEndEvent, useDndMonitor } from '@dnd-kit/core';
import { SortableContext, arrayMove, useSortable } from '@dnd-kit/sortable';
import { FileIcon, GripVerticalIcon, TrashIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

import useFormBuilder from '@/hooks/useFormBuilder';

function PagesSelector() {
    const { pages, setPage, current_page_id, removePage, addPage, setPages } =
        useFormBuilder();

    const handlePageChange = (pageId: string) => setPage(pageId);
    const handleRemovePage = (pageId: string) => removePage(pageId);

    useDndMonitor({
        onDragEnd: (event: DragEndEvent) => {
            const { active, over } = event;
            if (!active || !over) return;

            const isPageHandle = active.data.current?.isPageHandle;
            if (isPageHandle) {
                const activeId = active.data.current?.pageId;
                const overId = over.data.current?.pageId;

                const activeIdx = pages.findIndex(
                    (page) => page.page_id === activeId,
                );
                const overIdx = pages.findIndex(
                    (page) => page.page_id === overId,
                );
                setPages(arrayMove(pages, activeIdx, overIdx));
            }
        },
    });

    return (
        <div className='mt-4 space-y-2'>
            <SortableContext
                items={pages.map((page) => ({ id: page.page_id, ...page }))}
            >
                {pages.map((currentPage, i) => (
                    <PageItem
                        key={currentPage.page_id}
                        pageTitle={currentPage.page_title}
                        pageId={currentPage.page_id}
                        number={i + 1}
                        isActive={currentPage.page_id === current_page_id}
                        isDefault={currentPage.isDefault}
                        onPageChange={() =>
                            handlePageChange(currentPage.page_id)
                        }
                        onRemovePage={() =>
                            handleRemovePage(currentPage.page_id)
                        }
                    />
                ))}
            </SortableContext>
            <div className='flex justify-end'>
                <Button
                    onClick={addPage}
                    className='mt-4 gap-2 bg-metalic-blue hover:bg-metalic-blue/90'
                >
                    <FileIcon className='h-4 w-4' />
                    Add Page
                </Button>
            </div>
        </div>
    );
}

type PageItemProps = {
    pageTitle: string;
    pageId: string;
    number: number;
    isActive?: boolean;
    isDefault?: boolean;
    onPageChange: (pageId: string) => void;
    onRemovePage: (pageId: string) => void;
};

export function PageItem({
    number,
    pageTitle,
    pageId,
    onPageChange,
    onRemovePage,
    isActive = false,
    isDefault = false,
}: PageItemProps) {
    const sortable = useSortable({
        id: `page-handle-${number}`,
        data: {
            pageId,
            isPageHandle: true,
            activePageHandle: { number, pageTitle },
        },
    });

    if (sortable.isDragging) return null;

    return (
        <div
            ref={sortable.setNodeRef}
            onClick={() => onPageChange(pageId)}
            className={cn(
                'cursor-pointer select-none rounded-lg bg-metalic-blue/5 px-4 py-3 ring-2 ring-transparent transition',
                isActive && ' ring-metalic-blue',
            )}
        >
            <div className='flex justify-between'>
                <div className='flex gap-2 text-[14px]'>
                    <GripVerticalIcon
                        {...sortable.listeners}
                        {...sortable.attributes}
                        className='text-gray-400 outline-none'
                    />
                    <span className='font-semibold'>{`Page ${number}: ${
                        isDefault ? '(Default)' : ''
                    }`}</span>
                    {pageTitle || 'Untitled'}
                </div>
                <div>
                    <TrashIcon
                        className='h-5 w-5 text-red-500'
                        onClick={(e) => {
                            e.stopPropagation();
                            onRemovePage(pageId);
                        }}
                    />
                </div>
            </div>
        </div>
    );
}

export default PagesSelector;
