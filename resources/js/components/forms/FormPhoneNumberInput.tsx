import { LucideIcon } from 'lucide-react';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '../ui/form';
import { IMaskInput } from 'react-imask';
import { Control, FieldValues, Path } from 'react-hook-form';
import { cn } from '@/lib/utils';
import { PhFlag } from '@/assets/tenant/form';

type FormPhoneNumberInput<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
    showError?: boolean;
    placeholder?: string;
};

function FormPhoneNumberInput<T>({
    control,
    name,
    icon: Icon,
    showError = true,
    ...props
}: FormPhoneNumberInput<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field, fieldState }) => (
                <FormItem className='w-full'>
                    {props.label && (
                        <FormLabel className='text-[18px] font-medium'>
                            {props.label}
                        </FormLabel>
                    )}
                    <FormControl>
                        <div className='relative flex items-center gap-2'>
                            <div className='absolute left-4 flex items-center gap-2'>
                                <img
                                    src={PhFlag}
                                    alt='Philippine flag'
                                    className='h-[25px] object-contain'
                                />
                                <span className='text-black/50'>+63</span>
                            </div>
                            <IMaskInput
                                mask='000 0000 000'
                                value={field.value}
                                className={cn(
                                    'h-[60px] w-full rounded-lg border bg-[#F3F7FD] pl-24 pr-16 text-base outline-none placeholder:text-black/50 placeholder:text-gray-400 focus-visible:ring-transparent',
                                    {
                                        'border-red-500 bg-red-500/5 text-red-500':
                                            !!fieldState.error?.message,
                                    },
                                )}
                                onAccept={(value) => {
                                    const newValue = value.replace(
                                        /(\s+|\+63)/g,
                                        '',
                                    );
                                    field.onChange(newValue);
                                }}
                                {...props}
                            />
                            {Icon && (
                                <Icon className='absolute right-6 text-gray-400' />
                            )}
                        </div>
                    </FormControl>
                    {showError && <FormMessage />}
                </FormItem>
            )}
        />
    );
}

export default FormPhoneNumberInput;
