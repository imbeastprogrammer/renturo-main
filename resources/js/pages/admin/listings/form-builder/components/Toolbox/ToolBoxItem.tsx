import { CSSProperties } from 'react';
import { cn } from '@/lib/utils';
import { useSortable } from '@dnd-kit/sortable';
import { GripVertical, LucideIcon } from 'lucide-react';
import { CSS } from '@dnd-kit/utilities';

type ToolBoxItemProps = {
    title: string;
    icon: LucideIcon;
    id: string;
};

function ToolboxItem(props: ToolBoxItemProps) {
    const { setNodeRef, attributes, listeners, transform, transition } =
        useSortable({
            id: props.id,
            data: { type: props.id, toolboxItem: props, isToolboxItem: true },
        });

    const style: CSSProperties = {
        transition,
        transform: CSS.Transform.toString(transform),
    };

    return (
        <div
            ref={setNodeRef}
            style={style}
            className={cn(
                'flex select-none items-center justify-between gap-2 rounded-lg bg-blue-50 p-2 py-4',
            )}
        >
            <div className='flex flex-1 items-center gap-2 text-[14px] font-medium'>
                {props.icon && <props.icon className='text-metalic-blue' />}
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
