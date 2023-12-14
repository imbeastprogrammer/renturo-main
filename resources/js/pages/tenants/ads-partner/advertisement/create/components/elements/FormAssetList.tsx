import { useRef } from 'react';
import { HiOutlineUpload } from 'react-icons/hi';
import { Button } from '@/components/ui/button';

function FormAssetList() {
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
                />
                <p className='text-[11px] text-black/50'>
                    We accept JPEG, PNG, GIF, MP4, and MOV files.
                </p>
            </div>
        </div>
    );
}

export default FormAssetList;
