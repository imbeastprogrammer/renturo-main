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
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Listing } from '@/types/listings';

type ViewListingModalProps = {
    listings: Listing[];
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

const TabsMap: Record<string, React.FC<{ listing: Listing }>> = {
    overview: Overview,
    'review-details': ReviewDetails,
};

function ViewListingModal({
    isOpen,
    id,
    onClose,
    listings,
}: ViewListingModalProps) {
    const [activeTab, setActiveTab] = useState(navigationSelection[0].value);
    const listing = listings.find((listing) => listing.id === id);

    const CurrentTab = TabsMap[activeTab];
    if (!listing) return null;

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='grid h-full max-h-[750px] max-w-[1350px] grid-rows-[auto_auto_1fr] gap-y-0 overflow-hidden'>
                <DialogHeader className='mb-4'>
                    <div className='flex items-center justify-between'>
                        <DialogTitle className='text-[35px] font-semibold'>
                            {listing?.title}
                        </DialogTitle>
                        <button onClick={onClose}>
                            <img src={CloseIcon} alt='close button icon' />
                        </button>
                    </div>
                </DialogHeader>
                <Separator />
                <div className='grid h-full grid-cols-[250px_auto_1fr] overflow-hidden'>
                    <div className='p-4'>
                        <NavigationSelection
                            value={activeTab}
                            data={navigationSelection}
                            onValueChange={setActiveTab}
                        />
                    </div>
                    <Separator orientation='vertical' />
                    <ScrollArea className='p-4'>
                        <CurrentTab listing={listing!} />
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
                            { 'bg-[#EEF5FF]': currentValue === value },
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

type OverviewProps = { listing: Listing };
function Overview({ listing }: OverviewProps) {
    return (
        <div className='space-y-4'>
            <ImagesGrid listing_images={listing.images.map(({ url }) => url)} />
            <div>
                <h1 className='text-[22px]'>{listing.title}</h1>
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

type ImagesGridProps = { listing_images: string[] };
function ImagesGrid({ listing_images }: ImagesGridProps) {
    return (
        <div className='grid h-[300px] grid-cols-4 grid-rows-2 gap-4'>
            {listing_images.slice(0, 5).map((listing_image, idx) => (
                <img
                    key={idx}
                    src={listing_image}
                    alt='listing image'
                    className={cn(
                        'h-full w-full object-cover',
                        idx === 0 && 'col-span-2 row-span-2',
                    )}
                />
            ))}
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

type ReviewDetailsProps = { listing: Listing };
function ReviewDetails({ listing }: ReviewDetailsProps) {
    return (
        <div className='space-y-6'>
            <ReviewDetailsItem withUnderline={false} label='Post Id'>
                U-000-001
            </ReviewDetailsItem>
            <div className='grid grid-cols-2 gap-6'>
                <ReviewDetailsItem label='Post Name'>
                    Father Blanco’s Garden
                </ReviewDetailsItem>
                <ReviewDetialsImages />
            </div>
            <ReviewDetailsItem label='About'>
                Outside the San Agustin Museum is a little garden called Fr.
                Blanco’s Garden. This garden was named after Fr. Manuel Blanco
                who is considered the father of Philippine botany.
            </ReviewDetailsItem>
            <div className='grid grid-cols-2 gap-6'>
                <ReviewDetailsItem label='Host'>Jane Doe</ReviewDetailsItem>
                <ReviewDetailsItem label='Status'>Approved</ReviewDetailsItem>
            </div>
            <div className='grid grid-cols-2 gap-6'>
                <ReviewDetailsItem label='Created At'>
                    2023-10-20 14:14:00
                </ReviewDetailsItem>
                <ReviewDetailsItem label='Last Update'>
                    2023-10-20 14:14:00
                </ReviewDetailsItem>
            </div>
            <div className='grid grid-cols-2 gap-6'>
                <ReviewDetailsItem label='Category'>Venue</ReviewDetailsItem>
                <ReviewDetailsItem label='Subcategory'>
                    Garden
                </ReviewDetailsItem>
            </div>
            <div className='grid grid-cols-2 gap-6'>
                <ReviewDetailsItem label='Comments'>123</ReviewDetailsItem>
                <ReviewDetailsItem label='Views'>1,234</ReviewDetailsItem>
            </div>
        </div>
    );
}

type ReviewDetialsItem = {
    label: string;
    children: ReactNode;
    withUnderline?: boolean;
};

function ReviewDetailsItem({
    children,
    label,
    withUnderline = true,
}: ReviewDetialsItem) {
    return (
        <div className='flex w-full flex-1 items-center gap-4'>
            <span className='min-w-[100px] text-xl font-medium'>{label}</span>
            <span
                className={cn(
                    'block flex-1 p-2 text-black/50',
                    withUnderline && 'border-b',
                )}
            >
                {children}
            </span>
        </div>
    );
}

function ReviewDetialsImages() {
    return (
        <div className='flex items-center gap-4'>
            <span className='text-xl font-medium'>Images</span>
            <div className='flex gap-2'>
                <div className='h-[70px] w-[70px] bg-blue-500'></div>
                <div className='h-[70px] w-[70px] bg-blue-500'></div>
                <div className='h-[70px] w-[70px] bg-blue-500'></div>
                <div className='h-[70px] w-[70px] bg-blue-500'></div>
            </div>
            <Button
                variant='outline'
                className='border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue/90'
            >
                Preview
            </Button>
        </div>
    );
}

export default ViewListingModal;
