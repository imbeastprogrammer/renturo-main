import React, { ReactNode, useState } from 'react';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';

import {
    CapacityIcon,
    CloseIcon,
    DatesIcon,
    LocationIcon,
    TimeIcon,
    VenueIcon,
} from '@/assets/tenant/list-of-properties';
import dummyListings from '@/data/dummyListings';
import { cn } from '@/lib/utils';

type ViewListingModalProps = {
    isOpen: boolean;
    id: number;
    onClose: () => void;
};

const navigationSelection = [
    { label: 'Overview', value: 'overview' },
    {
        label: 'Review Details',
        value: 'review-details',
    },
];

const TabsMap: Record<string, React.FC> = {
    overview: Overview,
    'review-details': ReviewDetails,
};

function ViewListingModal({ isOpen, id, onClose }: ViewListingModalProps) {
    const [activeTab, setActiveTab] = useState(navigationSelection[0].value);
    const dummyListing = dummyListings.find(({ no }) => no === id);

    const CurrentTab = TabsMap[activeTab];

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='grid h-full max-h-[750px] max-w-[1300px] grid-rows-[auto_auto_1fr] gap-y-0 overflow-hidden'>
                <DialogHeader className='mb-4'>
                    <div className='flex items-center justify-between'>
                        <DialogTitle className='text-[35px] font-semibold'>
                            {dummyListing?.listing_name}
                        </DialogTitle>
                        <button onClick={onClose}>
                            <img src={CloseIcon} alt='close button icon' />
                        </button>
                    </div>
                </DialogHeader>
                <Separator />
                <div className='grid h-full grid-cols-[310px_auto_1fr] overflow-hidden'>
                    <div className='p-4'>
                        <NavigationSelection
                            value={activeTab}
                            data={navigationSelection}
                            onValueChange={setActiveTab}
                        />
                    </div>
                    <Separator orientation='vertical' />
                    <ScrollArea className='p-4'>
                        <CurrentTab />
                    </ScrollArea>
                </div>
            </DialogContent>
        </Dialog>
    );
}

type NavigationSelectionProps = {
    value: string;
    data: { label: string; value: string }[];
    onValueChange: (value: string) => void;
};
function NavigationSelection({
    data,
    value: currentValue,
    onValueChange,
}: NavigationSelectionProps) {
    return (
        <ul className='space-y-2'>
            {data.map(({ label, value }) => (
                <li key={value}>
                    <button
                        className={cn(
                            'w-full cursor-pointer rounded-full p-1 px-4 text-left text-[20px] font-semibold',
                            {
                                'bg-[#EEF5FF]': currentValue === value,
                            },
                        )}
                        onClick={() => onValueChange(value)}
                    >
                        {label}
                    </button>
                </li>
            ))}
        </ul>
    );
}

function Overview() {
    return (
        <div className='space-y-4'>
            <ImagesGrid />
            <div>
                <h1 className='text-[22px]'>Father Blanco's Garden</h1>
                <span className='textb-base font-bold'>PHP 15,000</span>
            </div>
            <Separator />
            <div className='grid grid-cols-3'>
                <ul className='space-y-2'>
                    <OverviewItem icon={LocationIcon}>
                        Intramuros, Manila
                    </OverviewItem>
                    <OverviewItem icon={VenueIcon}>Outdoor Venue</OverviewItem>
                    <OverviewItem icon={CapacityIcon}>1-10</OverviewItem>
                </ul>
                <ul className='space-y-2'>
                    <OverviewItem icon={DatesIcon}>
                        See available dates
                    </OverviewItem>
                    <OverviewItem icon={TimeIcon}>
                        1:00 PM - 5:00 PM
                    </OverviewItem>
                </ul>
            </div>
            <Separator />
            <div>
                <h2 className='text-lg font-medium'>About</h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Voluptatum, in officiis illum nulla enim pariatur minus quam
                    cum ratione corrupti.
                </p>
            </div>
            <Separator />
            <div>
                <h2 className='text-lg font-medium'>Policies</h2>
                <span className='text-metalic-blue hover:underline'>
                    Jane’s Policy
                </span>
            </div>
        </div>
    );
}

function ImagesGrid() {
    return (
        <div className='grid h-[300px] grid-cols-4 grid-rows-2 gap-4'>
            <div className='col-span-2 row-span-2 rounded-lg bg-blue-500'></div>
            <div className='rounded-lg bg-blue-500'></div>
            <div className='rounded-lg bg-blue-500'></div>
            <div className='rounded-lg bg-blue-500'></div>
            <div className='rounded-lg bg-blue-500'></div>
        </div>
    );
}

type OverviewItemProps = {
    icon: string;
    children: ReactNode;
};

function OverviewItem({ children, icon }: OverviewItemProps) {
    return (
        <li className='flex items-center gap-4 text-sm'>
            <img
                src={icon}
                className='h-[20px] w-[20px] object-contain'
                alt='overview item icon'
            />
            {children}
        </li>
    );
}

function ReviewDetails() {
    return <div>Review Details</div>;
}

export default ViewListingModal;
