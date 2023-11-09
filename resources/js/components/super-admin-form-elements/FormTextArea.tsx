import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { cn } from '@/lib/utils';
import { Control, FieldValues, Path } from 'react-hook-form';
import { Textarea, TextareaProps } from '../ui/textarea';

type FormTextAreaProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
} & TextareaProps;

function FormTextArea<T>({
    label,
    control,
    name,
    ...props
}: FormTextAreaProps<T>) {
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
                        <Textarea
                            {...props}
                            {...field}
                            className={cn(
                                'h-[30px] text-base placeholder:text-[#2E3436]/20 focus-visible:ring-picton-blue focus-visible:ring-offset-0',
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

export default FormTextArea;
