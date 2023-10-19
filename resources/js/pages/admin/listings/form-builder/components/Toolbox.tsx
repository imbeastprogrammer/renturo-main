import { LucideIcon } from 'lucide-react';
import ToolboxItem from './ToolBoxItem';

type ToolboxProps = {
    items: { label: string; items: Item[] }[];
};

type Item = { title: string; icon: LucideIcon; id: string };

function Toolbox({ items }: ToolboxProps) {
    return (
        <div className='rounded-lg border p-4 shadow-lg'>
            <div className='space-y-6'>
                {items.map(({ items, label }) => (
                    <div className='space-y-4' key={label}>
                        <h1 className='text-[15px] font-semibold'>{label}</h1>
                        <div className='grid grid-cols-2 gap-4'>
                            {items.map((item) => (
                                <ToolboxItem
                                    key={item.id}
                                    {...item}
                                    container={label}
                                />
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default Toolbox;
