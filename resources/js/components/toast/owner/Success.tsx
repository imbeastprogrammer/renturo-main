import { CheckIcon, XIcon } from 'lucide-react';

type SuccessProps = {
    title?: string;
    description?: string;
    onClose: () => void;
};

function Success({ title, description, onClose }: SuccessProps) {
    return (
        <div className='relative flex min-w-[350px] -translate-x-2 -translate-y-2 items-center gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='grid h-[58px] w-[58px] place-items-center rounded-full bg-metalic-blue text-white'>
                <CheckIcon className='h-[40px] w-[40px]' />
            </div>
            <div>
                <h1 className='text-base font-semibold'>
                    {title || 'Success'}
                </h1>
                <p className='text-xs text-black/50'>
                    {description ||
                        'Your password has been updated successfully.'}
                </p>
            </div>
            <button
                className='absolute right-2 top-2 text-[#D2D2D2]'
                onClick={onClose}
            >
                <XIcon />
            </button>
        </div>
    );
}

export default Success;
