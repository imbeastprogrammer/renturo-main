import { XIcon } from 'lucide-react';

type ErrorProps = {
    title?: string;
    description?: string;
};

function Error({ title, description }: ErrorProps) {
    return (
        <div className='flex items-center gap-4 rounded-lg bg-white p-4 shadow-sm'>
            <div className='grid h-[58px] w-[58px] place-items-center rounded-full bg-red-500 text-white'>
                <XIcon className='h-[40px] w-[40px]' />
            </div>
            <div>
                <h1 className='text-base font-semibold'>{title || 'Error'}</h1>
                <p className='text-xs text-black/50'>
                    {description ||
                        'Something went wrong, Please try again later'}
                </p>
            </div>
        </div>
    );
}

export default Error;
