import { XIcon } from 'lucide-react';

type ErrorProps = {
    title?: string;
    description?: string;
    onClose: () => void;
};

function Error({ title, description, onClose }: ErrorProps) {
    return (
        <div className='relative flex min-w-[350px] max-w-lg -translate-x-2 -translate-y-2 items-center gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='grid h-[58px] w-[58px] flex-shrink-0 place-items-center rounded-full bg-red-500 text-white'>
                <XIcon className='h-[40px] w-[40px]' />
            </div>
            <div>
                <h1 className='text-base font-semibold'>{title || 'Error'}</h1>
                <p className='text-xs text-black/50'>
                    {description ||
                        'Something went wrong, Please try again later'}
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

export default Error;
