import React, { PropsWithChildren } from 'react';

type DetailProps = {
    label: string;
} & PropsWithChildren;

function Detail({ label, children }: DetailProps) {
    return (
        <div>
            <h2 className='mb-2 text-lg font-medium'>{label}</h2>
            <p className='grid min-h-[60px] items-center rounded-lg bg-[#F3F7FD] p-4 text-base text-black/90'>
                {children}
            </p>
        </div>
    );
}

export default Detail;
