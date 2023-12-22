import { ReactNode, useState } from 'react';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import AdminLayout from '@/layouts/AdminLayout';
import AdvertisementInfoItem from './components/AdvertisementInfoItem';
import PromotionImages from './components/AdvertisementImages';
import ViewReceiptModal from './components/ViewReceiptModal';

function ViewAdvertisement() {
    const [viewReceiptModalState, setViewReceiptModalState] = useState({
        isOpen: false,
        id: 0,
    });

    return (
        <>
            <ScrollArea className='rounded-xl border bg-white p-4 shadow-lg'>
                <div className='mb-8'>
                    <h1 className='text-2xl font-semibold'>
                        Advertisement Details
                    </h1>
                    <p className='text-black/50'>
                        Preview an existing promotion.
                    </p>
                </div>
                <div className='space-y-6'>
                    <div className='grid grid-cols-2 gap-4'>
                        <AdvertisementInfoItem label='Promotion ID'>
                            U-000-001
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Status'>
                            Approved
                        </AdvertisementInfoItem>
                    </div>
                    <div className='grid grid-cols-2 items-center gap-4'>
                        <AdvertisementInfoItem label='Listing Name'>
                            Father Blanco’s Garden
                        </AdvertisementInfoItem>
                        <PromotionImages />
                    </div>
                    <AdvertisementInfoItem
                        label='Description'
                        alignment='start'
                    >
                        Escape the hustle and bustle of the city and discover a
                        hidden oasis of peace and serenity at Padre Blanco's
                        Garden, a beautifully landscaped haven in the heart of
                        Intramuros. Immerse yourself in the lush greenery,
                        stroll along the tranquil pathways, and enjoy the
                        soothing sounds of nature amidst the vibrant historical
                        backdrop of Intramuros.
                    </AdvertisementInfoItem>
                    <div className='grid grid-cols-2 items-center gap-4'>
                        <AdvertisementInfoItem label='Promotion Goal'>
                            Drive traffic into your property
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Duration'>
                            <div className='flex items-start justify-between gap-6'>
                                <div>
                                    <span className='text-base/30 text-xs font-medium uppercase'>
                                        Start
                                    </span>
                                    <p className='text-black/50'>
                                        November 08, 2023
                                    </p>
                                </div>
                                <div>
                                    <span className='text-base/30 text-xs font-medium uppercase'>
                                        End
                                    </span>
                                    <p className='text-black/50'>
                                        November 11, 2023
                                    </p>
                                </div>
                            </div>
                        </AdvertisementInfoItem>
                    </div>
                    <div className='grid grid-cols-2 gap-4'>
                        <AdvertisementInfoItem label='Budget'>
                            500 Php
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Payment Method'>
                            <div className='flex w-full items-center justify-between gap-4'>
                                <span>lorem ipsum</span>
                                <Button
                                    variant='outline'
                                    className='h-[32px] border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue/90'
                                    onClick={() =>
                                        setViewReceiptModalState({
                                            isOpen: true,
                                            id: 1,
                                        })
                                    }
                                >
                                    View Receipt
                                </Button>
                            </div>
                        </AdvertisementInfoItem>
                    </div>
                    <div className='mb-8'>
                        <h1 className='text-2xl font-semibold'>
                            Advertisement Activity
                        </h1>
                        <p className='text-black/50'>
                            Analyze post performance.
                        </p>
                    </div>
                    <div className='grid grid-cols-2 gap-4'>
                        <AdvertisementInfoItem label='Impressions'>
                            123
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Engagements'>
                            123
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Detail Expands'>
                            123
                        </AdvertisementInfoItem>
                        <AdvertisementInfoItem label='Sales'>
                            123
                        </AdvertisementInfoItem>
                    </div>
                </div>
            </ScrollArea>
            <ViewReceiptModal
                isOpen={viewReceiptModalState.isOpen}
                id={viewReceiptModalState.id}
                onClose={() =>
                    setViewReceiptModalState({ isOpen: false, id: 0 })
                }
            />
        </>
    );
}

ViewAdvertisement.layout = (page: ReactNode) => (
    <AdminLayout>{page}</AdminLayout>
);

export default ViewAdvertisement;
