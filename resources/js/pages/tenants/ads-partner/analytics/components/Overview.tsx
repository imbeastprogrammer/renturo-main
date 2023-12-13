import { PropsWithChildren } from 'react';
import { IconType } from 'react-icons';
import { FaEye, FaRegFolderOpen } from 'react-icons/fa6';
import { HiOutlineCursorClick } from 'react-icons/hi';

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

function Overview() {
    return (
        <div className='grid h-full grid-rows-[auto_1fr] gap-4 rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-center justify-between gap-4'>
                <h1 className='text-lg'>Overview</h1>
                <Select>
                    <SelectTrigger className='w-[144px] bg-black/5 text-black/40'>
                        <SelectValue placeholder='Filter' />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value='weekly'>Weekly</SelectItem>
                        <SelectItem value='monthly'>Monthly</SelectItem>
                        <SelectItem value='quarterly'>Quarterly</SelectItem>
                        <SelectItem value='yearly'>Yearly</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div className='grid grid-cols-2 gap-4'>
                <InfoItem value='1,400' icon={FaEye}>
                    Impressions
                </InfoItem>
                <InfoItem value='321' icon={HiOutlineCursorClick}>
                    Impressions
                </InfoItem>
                <InfoItem value='150' icon={FaRegFolderOpen}>
                    Detail Expands
                </InfoItem>
            </div>
        </div>
    );
}

type InfoItemProps = {
    value: string;
    icon: IconType;
} & PropsWithChildren;

function InfoItem({ value, children, icon: Icon }: InfoItemProps) {
    return (
        <div>
            <h2 className='text-[40px] font-medium leading-none text-metalic-blue'>
                {value}
            </h2>
            <div className='flex items-center gap-2'>
                <Icon className='h-[20px] w-[20px] text-arylide-yellow' />
                <p className='text-base'>{children}</p>
            </div>
        </div>
    );
}

export default Overview;
