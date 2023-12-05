import { useRef } from 'react';
import { Button } from '@/components/ui/button';
import { ProfilePicturePlaceholder } from '@/assets/central/settings';

type ProfilePicturePickerProps = {
    value: string | File;
    onChange: (file: File) => void;
    disabled?: boolean;
};

function ProfilePicturePicker({
    value,
    onChange,
    disabled = false,
}: ProfilePicturePickerProps) {
    const hiddenFileInputRef = useRef<HTMLInputElement | null>(null);

    const currentValue =
        typeof value === 'string' ? value : URL.createObjectURL(value);

    const handleUpload = () => hiddenFileInputRef.current?.click();

    return (
        <div className='flex gap-6'>
            <div className='h-[100px] w-[100px] flex-shrink-0 rounded-full border'>
                <img
                    src={currentValue || ProfilePicturePlaceholder}
                    alt='profile picture'
                    className='h-full w-full rounded-full object-cover'
                />
            </div>
            <input
                type='file'
                className='hidden'
                onChange={(e) => onChange(e.target.files?.[0] as File)}
                ref={hiddenFileInputRef}
                disabled={disabled}
            />
            <div>
                <h1 className='text-xl font-medium'>Profile Picture</h1>
                <p className='text-black/50'>
                    Max 400x400px. PNG or JPG Format.
                </p>
                <Button
                    onClick={handleUpload}
                    type='button'
                    variant='outline'
                    className='mt-2 px-6'
                    disabled={disabled}
                >
                    Upload
                </Button>
            </div>
        </div>
    );
}

export default ProfilePicturePicker;
