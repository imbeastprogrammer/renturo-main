import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { PropsWithChildren } from 'react';

function AdActivity() {
    return (
        <div className='h-full w-full space-y-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-center justify-between gap-4'>
                <h1 className='text-lg font-medium'>Ad Activity</h1>
                <Select>
                    <SelectTrigger className='w-[144px] bg-black/5 text-black/40'>
                        <SelectValue placeholder='[Ad Name]' />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value='sample_ad'>Sample Ad</SelectItem>
                        <SelectItem value='another_ad'>Another Ad</SelectItem>
                        <SelectItem value='example_ad'>Example Ad</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div className='space-y-4'>
                <InfoItem value='1,400'>Impressions</InfoItem>
                <InfoItem value='321'>Engagements</InfoItem>
                <InfoItem value='150'>Detail Expands</InfoItem>
            </div>
        </div>
    );
}

type InfoItemProps = { value: string } & PropsWithChildren;
function InfoItem({ value, children }: InfoItemProps) {
    return (
        <div className='flex justify-between gap-4'>
            <p className='text-base text-black/50'>{children}</p>
            <p className='text-base text-metalic-blue'>{value}</p>
        </div>
    );
}

export default AdActivity;
