import { format } from 'date-fns';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { HiCalendar } from 'react-icons/hi2';
import { Control, FieldValues, Path } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

type FormDurationSelectorProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
};

function FormDurationSelector<T>({
    label,
    control,
    name,
    description,
}: FormDurationSelectorProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem>
                    <FormLabel className='text-xl font-medium'>
                        {label}
                    </FormLabel>
                    <FormDescription className='font-medium text-black/50'>
                        {description}
                    </FormDescription>
                    <FormControl>
                        <Popover>
                            <PopoverTrigger asChild>
                                <div className='flex gap-2'>
                                    <Button
                                        variant='secondary'
                                        className='h-max gap-4 text-[15px] text-black/80'
                                    >
                                        <div>
                                            <span className='block text-left text-[10px] font-medium'>
                                                Start
                                            </span>
                                            {field.value[0]
                                                ? format(
                                                      field.value[0],
                                                      'MMMM dd, yyyy',
                                                  )
                                                : 'Pick a date'}
                                        </div>
                                        <HiCalendar className='h-[30px] w-[25px] text-metalic-blue' />
                                    </Button>

                                    <Button
                                        variant='secondary'
                                        className='h-max gap-4 text-[15px] text-black/80'
                                    >
                                        <div>
                                            <span className='block text-left text-[10px] font-medium'>
                                                Start
                                            </span>
                                            {field?.value[1]
                                                ? format(
                                                      field.value[1],
                                                      'MMMM dd, yyyy',
                                                  )
                                                : 'Pick a date'}
                                        </div>
                                        <HiCalendar className='h-[30px] w-[25px] text-metalic-blue' />
                                    </Button>
                                </div>
                            </PopoverTrigger>
                            <PopoverContent
                                className='w-auto p-0'
                                align='start'
                            >
                                <Calendar
                                    initialFocus
                                    mode='range'
                                    defaultMonth={field.value[0]}
                                    selected={{
                                        from: field.value[0],
                                        to: field.value[1],
                                    }}
                                    onSelect={(data) =>
                                        field.onChange([data?.from, data?.to])
                                    }
                                    numberOfMonths={2}
                                />
                            </PopoverContent>
                        </Popover>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormDurationSelector;
