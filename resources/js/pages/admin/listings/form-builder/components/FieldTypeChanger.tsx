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
            <SelectTrigger className='w-max gap-4 border-none text-[12px] ring-transparent focus:ring-transparent'>
                <div className='flex items-center gap-4'>
                    <div className='grid h-[30px] w-[30px] place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                        {Icon && <Icon className='h-[19px] w-[19px]' />}
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
