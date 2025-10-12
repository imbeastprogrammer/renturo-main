import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Control, FieldValues, Path } from 'react-hook-form';

type FormTextAreaProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
    data: { label: string; value: string; description?: string }[];
};

function FormRadioInput<T>({
    label,
    control,
    name,
    description,
    data,
}: FormTextAreaProps<T>) {
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
                        <RadioGroup
                            value={field.value}
                            onValueChange={field.onChange}
                        >
                            {data.map(({ label, value, description }) => (
                                <div
                                    key={value}
                                    className='flex items-center gap-4'
                                >
                                    <RadioGroupItem
                                        className='border-metalic-blue text-metalic-blue'
                                        value={value}
                                        id={label}
                                    />
                                    <div>
                                        <Label
                                            htmlFor={label}
                                            className='font-medium'
                                        >
                                            {label}
                                        </Label>
                                        {description && (
                                            <p className='text-sm text-black/50'>
                                                {description}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </RadioGroup>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormRadioInput;
