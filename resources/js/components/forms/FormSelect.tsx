import { LucideIcon } from 'lucide-react';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '../ui/form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
    data: { label: string; value: string }[];
    placeholder?: string;
};

// change the background of this based on the figma
function FormSelect<T>({
    control,
    name,
    icon: Icon,
    data,
    ...props
}: FormInputProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem className='w-full'>
                    {props.label && (
                        <FormLabel className='text-[18px] font-medium'>
                            {props.label}
                        </FormLabel>
                    )}
                    <FormControl>
                        <div className='relative flex items-center gap-2'>
                            <Select
                                value={field.value}
                                onValueChange={field.onChange}
                            >
                                <SelectTrigger className='h-[60px] rounded-lg bg-[#F3F7FD] p-6 text-base focus-visible:ring-transparent'>
                                    <SelectValue
                                        className='placeholder:text-black/50'
                                        placeholder={props.placeholder}
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    {data.map((d) => (
                                        <SelectItem
                                            key={d.value}
                                            value={d.value}
                                        >
                                            {d.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {Icon && (
                                <Icon className='absolute right-6 text-gray-400' />
                            )}
                        </div>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormSelect;
