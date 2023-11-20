import { XIcon } from 'lucide-react';

type ErrorProps = {
    title?: string;
    description?: string;
};

function Error({ title, description }: ErrorProps) {
    return (
        <div className='flex min-w-[350px] -translate-x-2 -translate-y-2 items-center gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <XIcon className='h-[40px] w-[40px] text-red-500' />
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
