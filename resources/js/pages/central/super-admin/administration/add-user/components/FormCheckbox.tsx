import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Checkbox } from '@/components/ui/checkbox';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
};

function FormCheckbox<T>({
    label,
    control,
    name,
    description,
}: FormInputProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem className='flex items-center space-y-0'>
                    <FormLabel className='min-w-[200px] text-base font-medium'>
                        {label}
                    </FormLabel>
                    <FormControl>
                        <Checkbox
                            checked={field.value}
                            onCheckedChange={(checked) =>
                                field.onChange(checked)
                            }
                            className='rounded-sm border-[#2E3436]/20 data-[state=checked]:bg-picton-blue'
                        />
                    </FormControl>
                    {description && (
                        <FormDescription className='ml-2 text-base text-black'>
                            {description}
                        </FormDescription>
                    )}
                </FormItem>
            )}
        />
    );
}

export default FormCheckbox;
