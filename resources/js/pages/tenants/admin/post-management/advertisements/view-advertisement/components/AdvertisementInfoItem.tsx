import { cn } from '@/lib/utils';
import { ReactNode } from 'react';

type AdvertisementInfoItemProps = {
    label: string;
    children: ReactNode;
    alignment?: 'start' | 'end' | 'center';
};

function AdvertisementInfoItem({
    label,
    children,
    alignment = 'center',
}: AdvertisementInfoItemProps) {
    return (
        <div className={cn('flex items-start gap-4', `items-${alignment}`)}>
            <span className='min-w-[150px] text-lg'>{label}</span>
            <span className='block w-full text-black/50'>{children}</span>
        </div>
    );
}

export default AdvertisementInfoItem;
