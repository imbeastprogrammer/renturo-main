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

type FormPaymentMethodSelectionProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
    data: { label: string; value: string; details: Record<string, string> }[];
};

function FormPaymentMethodSelection<T>({
    label,
    control,
    name,
    description,
    data,
}: FormPaymentMethodSelectionProps<T>) {
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
                            {data.map(({ label, value, details }) => (
                                <div
                                    key={value}
                                    className='flex items-center gap-4 text-base font-medium'
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
                                        {value === field.value &&
                                            Object.entries(details).map(
                                                ([key, value]) => (
                                                    <div className='flex gap-2'>
                                                        <span className='capitalize text-black/50'>
                                                            {key}:
                                                        </span>
                                                        <span className='text-metalic-blue'>
                                                            {value}
                                                        </span>
                                                    </div>
                                                ),
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

export default FormPaymentMethodSelection;
