import { cn } from '@/lib/utils';
import { useDraggable } from '@dnd-kit/core';
import { LucideIcon } from 'lucide-react';

type ToolBoxItemProps = {
    title: string;
    icon: LucideIcon;
    id: string;
};

function ToolboxItem({ title, ...props }: ToolBoxItemProps) {
    const { setNodeRef, attributes, listeners, isDragging } = useDraggable({
        id: props.id,
    });

    return (
        <div
            ref={setNodeRef}
            {...listeners}
            {...attributes}
            className={cn(
                'flex items-center justify-between gap-2 rounded-lg border-2 border-dashed bg-blue-50 px-4 py-3',
                isDragging && 'text-transparent',
            )}
        >
            {title}
            <props.icon />
        </div>
    );
}

export default ToolboxItem;
