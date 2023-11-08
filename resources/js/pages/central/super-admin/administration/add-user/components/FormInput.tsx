import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Input, InputProps } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
} & InputProps;

function FormInput<T>({ label, control, name, ...props }: FormInputProps<T>) {
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
                        <Input
                            {...props}
                            {...field}
                            className={cn(
                                'text-base placeholder:text-[#2E3436]/20 focus-visible:ring-picton-blue focus-visible:ring-offset-0',
                                !!fieldState.error?.message &&
                                    'ring-2 ring-red-500 focus-visible:ring-red-500',
                            )}
                        />
                    </FormControl>
                </FormItem>
            )}
        />
    );
}

export default FormInput;
