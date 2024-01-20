import { XIcon } from 'lucide-react';

type ErrorProps = {
    title?: string;
    description?: string;
    onClose: () => void;
};

function Error({ title, description, onClose }: ErrorProps) {
    return (
        <div className='flex min-w-[350px] max-w-lg -translate-x-2 -translate-y-2 items-center gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <XIcon className='h-[40px] w-[40px] flex-shrink-0 text-red-500' />
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
