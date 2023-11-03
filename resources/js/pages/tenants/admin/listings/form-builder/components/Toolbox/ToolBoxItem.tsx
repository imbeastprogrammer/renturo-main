import React from 'react';
import { cn } from '@/lib/utils';
import { GripVertical } from 'lucide-react';
import { useDraggable } from '@dnd-kit/core';

type ToolBoxItemProps = {
    title: string;
    icon: React.FC;
    type: string;
};

function ToolboxItem(props: ToolBoxItemProps) {
    const { setNodeRef, attributes, listeners } = useDraggable({
        id: props.type,
        data: { type: props.type, toolboxItem: props, isToolboxItem: true },
    });

    return (
        <div
            ref={setNodeRef}
            className={cn(
                'flex select-none items-center justify-between gap-2 rounded-lg bg-blue-50 p-2 py-4',
            )}
        >
            <div className='flex flex-1 items-center gap-2 text-[14px] font-medium'>
                {props.icon && <props.icon />}
                {props.title}
            </div>
            <GripVertical
                className='h-6 w-6 text-gray-400 outline-none'
                {...listeners}
                {...attributes}
            />
        </div>
    );
}

export default ToolboxItem;
