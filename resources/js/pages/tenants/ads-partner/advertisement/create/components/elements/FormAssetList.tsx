import { XIcon } from 'lucide-react';
import React, { useRef } from 'react';
import { HiOutlineUpload } from 'react-icons/hi';
import { Control, FieldValues, Path } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import _ from 'lodash';

type FormAssetListProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    type?: 'single' | 'multiple';
};

interface PickerProps {
    isMultiple?: boolean;
    value: File[];
    onChange: (value: File[]) => void;
}

const ImagePickerMap: Record<string, React.FC<PickerProps>> = {
    multiple: MultiplePicker,
    single: SinglePicker,
    no_data: EmptyPicker,
};

function FormAssetList<T>({
    label,
    control,
    name,
    type = 'single',
}: FormAssetListProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => {
                const correctPicker = !field.value.length ? 'no_data' : type;
                const ImagePicker = ImagePickerMap[correctPicker];

                return (
                    <FormItem className='space-y-4'>
                        <FormLabel className='text-xl font-medium'>
                            {label}
                        </FormLabel>
                        <FormControl>
                            <ImagePicker
                                value={field.value}
                                isMultiple={type === 'multiple'}
                                onChange={(value) => field.onChange(value)}
                            />
                        </FormControl>
                    </FormItem>
                );
            }}
        />
    );
}

function EmptyPicker({ onChange, isMultiple = false }: PickerProps) {
    const hiddenFileInputRef = useRef<HTMLInputElement>(null);
    const handleUploadFile = () => hiddenFileInputRef.current?.click();

    return (
        <div className='grid h-[345px] place-items-center rounded-lg border-4 border-dashed border-metalic-blue bg-metalic-blue/5'>
            <div className='grid place-items-center gap-4'>
                <div className='grid h-[88px] w-[88px] place-items-center rounded-full bg-white'>
                    <HiOutlineUpload className='h-[44px] w-[44px] text-metalic-blue' />
                </div>
                <p className='text-lg text-metalic-blue'>
                    Upload a media that will make your business shine!
                </p>
                <Button
                    type='button'
                    onClick={handleUploadFile}
                    className='bg-metalic-blue/80 text-base hover:bg-metalic-blue/70'
                >
                    Upload File
                </Button>
                <input
                    ref={hiddenFileInputRef}
                    type='file'
                    className='hidden'
                    multiple={isMultiple}
                    onChange={(e) =>
                        e.target.files?.length && onChange([...e.target.files])
                    }
                />
                <p className='text-[11px] text-black/50'>
                    We accept JPEG, PNG, GIF, MP4, and MOV files.
                </p>
            </div>
        </div>
    );
}

function SinglePicker({ onChange, value }: PickerProps) {
    const hiddenFileInputRef = useRef<HTMLInputElement>(null);
    const imageUrl = value.length > 0 ? URL.createObjectURL(value[0]) : '';

    const handleUploadFile = () => hiddenFileInputRef.current?.click();

    return (
        <div className='flex items-center gap-4'>
            <div className='h-[70px] w-[212px] overflow-hidden rounded-lg'>
                <img
                    src={imageUrl}
                    alt='image picked'
                    className='h-full w-full object-cover'
                />
            </div>
            <input
                ref={hiddenFileInputRef}
                type='file'
                className='hidden'
                onChange={(e) =>
                    e.target.files?.length && onChange([...e.target.files])
                }
            />
            <Button
                variant='outline'
                onClick={handleUploadFile}
                className='border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
            >
                Upload
            </Button>
        </div>
    );
}

function MultiplePicker({ onChange, value, isMultiple }: PickerProps) {
    const hiddenFileInputRef = useRef<HTMLInputElement>(null);
    const imageUrls = value.map((val) => URL.createObjectURL(val));

    const handleUploadFile = () => hiddenFileInputRef.current?.click();
    const onRemove = (idx: number) => {
        onChange(_.filter(value, (_, i) => i !== idx));
    };

    return (
        <div className='flex items-center gap-4'>
            {imageUrls.map((img, idx) => (
                <div key={idx} className='relative h-[70px] w-[70px]'>
                    <button
                        type='button'
                        className='absolute -right-2 -top-2 grid h-[20px] w-[20px] place-items-center rounded-full bg-gray-200'
                        onClick={() => onRemove(idx)}
                    >
                        <XIcon className='h-4 w-4 text-gray-500' />
                    </button>
                    <img
                        src={img}
                        alt='image picked'
                        className='h-[70px] w-[70px] rounded-lg object-cover'
                    />
                </div>
            ))}
            <input
                ref={hiddenFileInputRef}
                type='file'
                className='hidden'
                onChange={(e) =>
                    e.target.files?.length && onChange([...e.target.files])
                }
                multiple={isMultiple}
                max={4}
            />
            <Button
                variant='outline'
                onClick={handleUploadFile}
                className='border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
            >
                Upload
            </Button>
        </div>
    );
}

export default FormAssetList;
