import { ReactNode } from 'react';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';

import {
    CapacityIcon,
    CloseIcon,
    DatesIcon,
    LocationIcon,
    TimeIcon,
    VenueIcon,
} from '@/assets/tenant/list-of-properties';
import dummyListings from '@/data/dummyListings';

type ViewListingModalProps = {
    isOpen: boolean;
    id: number;
    onClose: () => void;
};

function ViewListingModal({ isOpen, id, onClose }: ViewListingModalProps) {
    const dummyListing = dummyListings.find(({ no }) => no === id);

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='h-full max-h-[750px] max-w-[1300px] overflow-hidden'>
                <DialogHeader className='mb-0'>
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
                <div className='grid h-full grid-cols-[310px_auto_1fr] gap-x-4 overflow-hidden'>
                    <div>
                        <h1>Overview</h1>
                        <h1>Review Details</h1>
                    </div>
                    <Separator orientation='vertical' />
                    <ScrollArea className='pr-4'>
                        <Overview />
                    </ScrollArea>
                </div>
            </DialogContent>
        </Dialog>
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

export default ViewListingModal;
