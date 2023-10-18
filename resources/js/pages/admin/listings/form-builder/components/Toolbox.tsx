import { LucideIcon } from 'lucide-react';
import ToolboxItem from './ToolBoxItem';

type ToolboxProps = {
    items: { title: string; icon: LucideIcon; id: string }[];
};

function Toolbox({ items }: ToolboxProps) {
    return (
        <div className='rounded-lg border p-4 shadow-lg'>
            <div className='space-y-2'>
                {items.map((toolboxItem) => (
                    <ToolboxItem key={toolboxItem.id} {...toolboxItem} />
                ))}
            </div>
        </div>
    );
}

export default Toolbox;
