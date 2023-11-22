import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { cn } from '@/lib/utils';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormGenderPickerProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
    data: { label: string; value: string }[];
};

function FormGenderPicker<T>({
    label,
    control,
    name,
    description,
    data,
}: FormGenderPickerProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem>
                    <FormLabel className='text-xl font-medium'>
                        {label}
                    </FormLabel>
                    <FormDescription className='text-base font-medium text-black/50'>
                        {description}
                    </FormDescription>
                    <FormControl>
                        <div className='flex gap-2'>
                            {data.map(({ label, value }) => (
                                <Button
                                    key={value}
                                    variant='secondary'
                                    onClick={() => field.onChange(value)}
                                    className={cn(
                                        'flex-1 text-lg font-medium',
                                        {
                                            'text-metalic-blue':
                                                value === field.value,
                                        },
                                    )}
                                >
                                    {label}
                                </Button>
                            ))}
                        </div>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormGenderPicker;
