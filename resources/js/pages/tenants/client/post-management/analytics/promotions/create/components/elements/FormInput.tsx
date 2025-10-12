import { LucideIcon } from 'lucide-react';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input, InputProps } from '@/components/ui/input';
import { Control, FieldValues, Path } from 'react-hook-form';
import { cn } from '@/lib/utils';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
} & InputProps;

function FormInput<T>({ control, name, ...props }: FormInputProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <FormItem className='w-full'>
                    {props.label && (
                        <FormLabel className='text-xl font-medium'>
                            {props.label}
                        </FormLabel>
                    )}
                    <FormControl>
                        <div className='relative flex items-center gap-2'>
                            <Input
                                className={cn(
                                    'h-[45px] rounded-lg bg-[#F3F7FD] text-base placeholder:text-black/50 placeholder:text-gray-400 focus-visible:ring-transparent',
                                    {
                                        'border-red-500 bg-red-500/5 text-red-500':
                                            !!fieldState.error?.message,
                                    },
                                )}
                                {...field}
                                {...props}
                            />
                        </div>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormInput;
