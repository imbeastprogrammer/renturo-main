import { LucideIcon } from 'lucide-react';
import { Control, FieldValues, Path } from 'react-hook-form';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type FormAdsButtonPickerProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
    data: { label: string; value: string }[];
    placeholder?: string;
    disabled?: boolean;
};

function FormAdsButtonPicker<T>({
    control,
    name,
    icon: Icon,
    data,
    label,
    disabled,
    placeholder,
}: FormAdsButtonPickerProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
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
                                <SelectTrigger className='h-[45px] rounded-lg bg-[#F3F7FD] p-6 text-lg font-medium focus-visible:ring-transparent'>
                                    <SelectValue
                                        className='placeholder:text-black/50'
                                        placeholder={placeholder}
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

export default FormAdsButtonPicker;
