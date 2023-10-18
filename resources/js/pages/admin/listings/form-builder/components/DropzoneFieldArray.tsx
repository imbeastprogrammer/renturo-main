import { useEffect, useRef } from 'react';
import {
    DndContext,
    DragEndEvent,
    closestCenter,
    useDroppable,
} from '@dnd-kit/core';
import {
    SortableContext,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { restrictToVerticalAxis } from '@dnd-kit/modifiers';

import DropzoneItem from './DropzoneItem';
import { FormFields } from '..';

type DropzoneProps = {
    items: FormFields[];
    isDragging: boolean;
    onRemove: (idx: number) => void;
    onSort: (activeIdx: number, overIdx: number) => void;
};

function DropzoneFieldArray<T>({
    items,
    isDragging,
    onRemove,
    onSort,
}: DropzoneProps) {
    const lastElement = useRef<HTMLDivElement>(null);

    const { setNodeRef } = useDroppable({
        id: 'droppable',
    });

    const handleDragEnd = (e: DragEndEvent) => {
        const { active, over } = e;
        const activeIdx = active.data.current?.sortable.index;
        const overIdx = over?.data.current?.sortable.index;

        if (activeIdx !== overIdx) onSort(activeIdx, overIdx);
    };

    useEffect(() => {
        lastElement.current?.scrollIntoView();
    }, [items.length]);

    return (
        <div className='relative overflow-hidden'>
            <div
                ref={setNodeRef}
                className='hide-scrollbar relative h-full overflow-y-auto overflow-x-hidden rounded-lg border-2 border-dashed bg-blue-50 p-4 shadow-lg'
            >
                <DndContext
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                    modifiers={[restrictToVerticalAxis]}
                >
                    <SortableContext
                        items={items}
                        strategy={verticalListSortingStrategy}
                    >
                        <div className='space-y-2'>
                            {items.map((item, i) => (
                                <DropzoneItem
                                    key={item.id}
                                    item={item}
                                    onRemove={() => onRemove(i)}
                                />
                            ))}
                            <div ref={lastElement}></div>
                        </div>
                    </SortableContext>
                </DndContext>
            </div>
            {(isDragging || items.length === 0) && (
                <div className='absolute inset-0 z-[100] grid place-items-center backdrop-blur-sm'>
                    <h1 className='text-center text-[20px] text-gray-500'>
                        Drop item from toolbox
                    </h1>
                </div>
            )}
        </div>
    );
}

export default DropzoneFieldArray;
