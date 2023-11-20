import { CheckIcon } from 'lucide-react';

type SuccessProps = { title?: string; description?: string };

function Success({ title, description }: SuccessProps) {
    return (
        <div className='flex min-w-[350px] -translate-x-2 -translate-y-2 items-center gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <CheckIcon className='h-[40px] w-[40px] text-green-500' />
            <div>
                <h1 className='text-base font-semibold'>
                    {title || 'Success'}
                </h1>
                <p className='text-xs text-black/50'>
                    {description ||
                        'Your password has been updated successfully.'}
                </p>
            </div>
        </div>
    );
}

export default Success;
