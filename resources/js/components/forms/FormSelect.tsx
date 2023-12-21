import { LucideIcon } from 'lucide-react';
import { Control, FieldValues, Path } from 'react-hook-form';
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
import { cn } from '@/lib/utils';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
    data: { label: string; value: string }[];
    placeholder?: string;
    disabled?: boolean;
    className?: string;
};

// change the background of this based on the figma
function FormSelect<T>({
    control,
    name,
    icon: Icon,
    data,
    label,
    disabled,
    placeholder,
    className,
}: FormInputProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <FormItem className='w-full'>
                    {label && (
                        <FormLabel className='text-[18px] font-medium'>
                            {label}
                        </FormLabel>
                    )}
                    <FormControl>
                        <div className='relative flex items-center gap-2'>
                            <Select
                                value={field.value}
                                onValueChange={field.onChange}
                                disabled={disabled}
                            >
                                <SelectTrigger
                                    className={cn(
                                        'h-[60px] rounded-lg bg-[#F3F7FD] p-6 text-base focus-visible:ring-transparent',
                                        {
                                            'border-red-500 bg-red-500/5 focus:ring-transparent':
                                                fieldState.error?.message,
                                        },
                                        className,
                                    )}
                                >
                                    <SelectValue
                                        className='placeholder:text-black/50'
                                        placeholder={placeholder}
                                    />
                                </SelectTrigger>
                                <SelectContent className='max-h-[300px]'>
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
