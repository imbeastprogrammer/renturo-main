import { useRef } from 'react';
import { Control, FieldValues, Path } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

type IconPickerProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
};

function IconPicker<T>({
    label,
    name,
    control,
    description,
}: IconPickerProps<T>) {
    const hiddenPickerRef = useRef<HTMLInputElement>(null);

    const handlePick = () => hiddenPickerRef.current?.click();

    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem className='w-full'>
                    <FormLabel className='text-xl font-medium'>
                        {label}
                    </FormLabel>
                    <FormDescription className='text-[15px] font-medium text-black/50'>
                        {description}
                    </FormDescription>
                    <FormControl>
                        <div>
                            <input
                                ref={hiddenPickerRef}
                                type='file'
                                name='icon'
                                id='icon'
                                className='hidden'
                                onChange={(e) =>
                                    e.target.files?.length &&
                                    field.onChange(e.target.files[0])
                                }
                            />
                            <Button
                                onClick={handlePick}
                                variant='outline'
                                className='border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
                                type='button'
                            >
                                Upload
                            </Button>
                        </div>
                    </FormControl>
                    <FormDescription>{field.value?.name}</FormDescription>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default IconPicker;
