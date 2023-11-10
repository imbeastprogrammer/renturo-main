import { LucideIcon } from 'lucide-react';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '../ui/form';
import { Input, InputProps } from '../ui/input';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
} & InputProps;

// change the background of this based on the figma
function FormInput<T>({
    control,
    name,
    icon: Icon,
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
                            <Input
                                className='rounded-lg bg-[#F3F7FD] p-6 pr-16 text-base placeholder:text-black/50 placeholder:text-gray-400 focus-visible:ring-transparent'
                                {...field}
                                {...props}
                            />
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

export default FormInput;
