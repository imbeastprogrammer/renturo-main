import { useEffect, useRef } from 'react';
import { useDroppable } from '@dnd-kit/core';
import DropzoneItem from './DropzoneItem';
import { DropzoneField } from '..';

type DropzoneProps = { items: DropzoneField[]; isDragging: boolean };

function Dropzone({ items, isDragging }: DropzoneProps) {
    const lastElement = useRef<HTMLDivElement>(null);
    const { setNodeRef } = useDroppable({
        id: 'droppable',
    });

    useEffect(() => {
        lastElement.current?.scrollIntoView();
    }, [items.length]);

    return (
        <div className='relative overflow-hidden'>
            <div
                ref={setNodeRef}
                className='relative h-full overflow-auto rounded-lg border-2 border-dashed bg-blue-50 p-4 shadow-lg'
            >
                <div className='space-y-2 overflow-auto'>
                    {items.map((item, i) => (
                        <DropzoneItem key={i} item={item} />
                    ))}
                    <div ref={lastElement}></div>
                </div>
            </div>
            {isDragging && (
                <div className='absolute inset-0 z-[100] grid place-items-center backdrop-blur-sm'>
                    <h1 className='text-center text-[20px] text-gray-500'>
                        Drop item from toolbox
                    </h1>
                </div>
            )}
        </div>
    );
}

export default Dropzone;
