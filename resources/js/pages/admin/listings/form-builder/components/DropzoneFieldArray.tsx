import { useRef } from 'react';
import { Control, FieldValues } from 'react-hook-form';
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

import { FormFields } from '..';
import DropzoneItem from './DropzoneItem';
import EmptyDropzone from './EmptyDropzone';

type DropzoneProps = {
    items: FormFields[];
    isDragging: boolean;
    onRemove: (idx: number) => void;
    onSort: (activeIdx: number, overIdx: number) => void;
};

function DropzoneFieldArray({ items, onRemove, onSort }: DropzoneProps) {
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

    return (
        <div ref={setNodeRef} className='relative overflow-hidden'>
            <div className='hide-scrollbar relative h-full overflow-y-auto overflow-x-hidden bg-[#f4f4f4] p-4 shadow-lg'>
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
                            {items.map((item, idx) => (
                                <DropzoneItem
                                    key={item.id}
                                    item={item}
                                    index={idx}
                                    onRemove={() => onRemove(idx)}
                                />
                            ))}
                            <div ref={lastElement}></div>
                        </div>
                    </SortableContext>
                </DndContext>
                {!items.length && <EmptyDropzone />}
            </div>
        </div>
    );
}

export default DropzoneFieldArray;
