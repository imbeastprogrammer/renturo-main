import { useRef } from 'react';
import { Control, FieldValues, Path } from 'react-hook-form';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormMessage,
} from '@/components/ui/form';
import { RiUpload2Fill } from 'react-icons/ri';
import { Button } from '@/components/ui/button';

type FormProofOfPaymentPickerProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
};

function FormProofOfPaymentPicker<T>({
    label,
    control,
    name,
}: FormProofOfPaymentPickerProps<T>) {
    const hiddenFileInputRef = useRef<HTMLInputElement | null>(null);
    const handleUpload = () => hiddenFileInputRef.current?.click();

    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem>
                    <FormControl>
                        <div>
                            <input
                                ref={hiddenFileInputRef}
                                className='hidden'
                                type='file'
                                onChange={(e) => {
                                    if (e.target.files)
                                        field.onChange(e.target.files[0]);
                                }}
                            />
                            <Button
                                className='min-w-[252px] gap-4 bg-metalic-blue text-sm font-medium hover:bg-metalic-blue/90'
                                onClick={handleUpload}
                            >
                                <RiUpload2Fill className='h-4 w-4' />
                                {label}
                            </Button>
                        </div>
                    </FormControl>
                    <FormDescription className='text-base font-medium text-black/50'>
                        {field.value?.name}
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormProofOfPaymentPicker;
