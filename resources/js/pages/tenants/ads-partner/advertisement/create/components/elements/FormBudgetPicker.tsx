import * as React from 'react';
import * as SliderPrimitive from '@radix-ui/react-slider';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Control, FieldValues, Path } from 'react-hook-form';
import { cn } from '@/lib/utils';
import formatCurrency from '@/lib/formatCurrency';

const Slider = React.forwardRef<
    React.ElementRef<typeof SliderPrimitive.Root>,
    React.ComponentPropsWithoutRef<typeof SliderPrimitive.Root>
>(({ className, ...props }, ref) => (
    <SliderPrimitive.Root
        ref={ref}
        className={cn(
            'relative flex w-full touch-none select-none items-center',
            className,
        )}
        {...props}
    >
        <SliderPrimitive.Track className='relative h-2 w-full grow overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800'>
            <SliderPrimitive.Range className='absolute h-full bg-metalic-blue' />
        </SliderPrimitive.Track>
        <SliderPrimitive.Thumb className='block h-5 w-5 rounded-full border bg-white  transition-colors focus-visible:outline-none focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-50' />
    </SliderPrimitive.Root>
));

Slider.displayName = SliderPrimitive.Root.displayName;

type FormBudgetPickerProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    min: number;
    max: number;
    steps?: number;
};

function FormBudgetPicker<T>({
    label,
    control,
    name,
    min,
    max,
    steps,
}: FormBudgetPickerProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem className='space-y-4'>
                    <FormLabel className='text-xl font-medium'>
                        {label}
                    </FormLabel>
                    <FormDescription className='text-center text-base font-medium text-black/50'>
                        Projected number of accounts reached per day:{' '}
                        <span className='text-metalic-blue'>
                            534 - 1,2K Accounts
                        </span>
                    </FormDescription>
                    <div className='flex justify-center'>
                        <h1 className='text-[48px] font-semibold leading-none'>
                            {formatCurrency(parseFloat(field.value))}
                        </h1>
                    </div>
                    <FormControl>
                        <div className='flex gap-2'>
                            <span className='text-arylide-yellow'>
                                {formatCurrency(min)}
                            </span>
                            <Slider
                                value={field.value}
                                max={max}
                                step={steps}
                                minStepsBetweenThumbs={10}
                                min={min}
                                onValueChange={field.onChange}
                            />
                            <span className='text-arylide-yellow'>
                                {formatCurrency(max)}
                            </span>
                        </div>
                    </FormControl>
                </FormItem>
            )}
        />
    );
}

export default FormBudgetPicker;
