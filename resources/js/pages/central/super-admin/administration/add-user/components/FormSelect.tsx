import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Control, FieldValues, Path } from 'react-hook-form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

type FormSelectProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    placeholder?: string;
    data: { label: string; value: string }[];
};

function FormSelect<T>({
    label,
    control,
    name,
    data,
    placeholder = 'Select Option',
}: FormSelectProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <FormItem className='flex items-center space-y-0'>
                    <FormLabel className='min-w-[200px] text-base font-medium'>
                        {label}
                    </FormLabel>
                    <FormControl>
                        <Select
                            value={field.value}
                            onValueChange={field.onChange}
                        >
                            <SelectTrigger
                                className={cn(
                                    'w-[180px] focus-visible:ring-picton-blue focus-visible:ring-offset-0 active:ring-picton-blue',
                                    !!fieldState.error?.message &&
                                        'ring-2 ring-red-500 focus-visible:ring-red-500',
                                )}
                            >
                                <SelectValue
                                    placeholder={placeholder}
                                    className='text-base text-[#2E3436]'
                                />
                            </SelectTrigger>
                            <SelectContent>
                                {data.map(({ label, value }, idx) => (
                                    <SelectItem key={idx} value={value}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormControl>
                </FormItem>
            )}
        />
    );
}

export default FormSelect;
