import React from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ElementsType } from './FormElement';

type FieldTypeProps = {
    icon?: React.FC;
    value?: ElementsType;
    onValueChange: (value: ElementsType) => void;
    data: { id: ElementsType; title: string; icon: React.FC }[];
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
                    <div className='grid h-[35px] w-[35px] place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
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
