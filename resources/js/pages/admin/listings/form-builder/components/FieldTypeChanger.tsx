import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { LucideIcon } from 'lucide-react';
import { ElementsType } from './FormElement';

type FieldTypeProps = {
    icon?: LucideIcon;
    value?: ElementsType;
    onValueChange: (value: ElementsType) => void;
    data: { id: ElementsType; title: string; icon: LucideIcon }[];
};

function FieldTypeChanger({
    value,
    icon: Icon,
    onValueChange,
    data,
}: FieldTypeProps) {
    return (
        <Select value={value} onValueChange={onValueChange}>
            <SelectTrigger className='w-max gap-4 border-none ring-transparent focus:ring-transparent'>
                <div className='flex items-center gap-4'>
                    <div className='grid h-10 w-10 place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                        {Icon && <Icon />}
                    </div>
                </div>
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                {data.map((item) => (
                    <SelectItem key={item.id} value={item.id}>
                        {item.title}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}

export default FieldTypeChanger;
