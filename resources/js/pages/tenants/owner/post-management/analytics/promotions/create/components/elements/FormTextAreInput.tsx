import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { cn } from '@/lib/utils';
import { Control, FieldValues, Path } from 'react-hook-form';
import { Textarea, TextareaProps } from '@/components/ui/textarea';

type FormTextAreaProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
} & TextareaProps;

function FormTextAreaInput<T>({
    label,
    control,
    name,
    description,
    ...props
}: FormTextAreaProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <FormItem>
                    <FormLabel className='text-xl font-medium'>
                        {label}
                    </FormLabel>
                    <FormDescription className='font-medium text-black/50'>
                        {description}
                    </FormDescription>
                    <FormControl>
                        <Textarea
                            {...props}
                            {...field}
                            className={cn(
                                'h-[30px] bg-[#F3F7FD] text-base placeholder:text-black/50 placeholder:text-gray-400 focus-visible:ring-transparent',
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

export default FormTextAreaInput;
