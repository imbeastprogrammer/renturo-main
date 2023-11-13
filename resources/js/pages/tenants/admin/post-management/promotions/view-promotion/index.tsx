import { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout';
import PromotionInfoItem from './components/PromotionInfoItem';
import PromotionImages from './components/PromotionImages';
import { ScrollArea } from '@/components/ui/scroll-area';

function ViewPromotion() {
    return (
        <ScrollArea className='rounded-xl border bg-white p-4 shadow-lg'>
            <div className='mb-8'>
                <h1 className='text-2xl font-semibold'>Promotion Details</h1>
                <p className='text-black/50'>Preview an existing promotion.</p>
            </div>
            <div className='space-y-6'>
                <div className='grid grid-cols-2 gap-4'>
                    <PromotionInfoItem label='Promotion ID'>
                        U-000-001
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Status'>
                        Approved
                    </PromotionInfoItem>
                </div>
                <div className='grid grid-cols-2 items-center gap-4'>
                    <PromotionInfoItem label='Listing Name'>
                        Father Blanco’s Garden
                    </PromotionInfoItem>
                    <PromotionImages />
                </div>
                <PromotionInfoItem label='Description' alignment='start'>
                    Escape the hustle and bustle of the city and discover a
                    hidden oasis of peace and serenity at Padre Blanco's Garden,
                    a beautifully landscaped haven in the heart of Intramuros.
                    Immerse yourself in the lush greenery, stroll along the
                    tranquil pathways, and enjoy the soothing sounds of nature
                    amidst the vibrant historical backdrop of Intramuros.
                </PromotionInfoItem>
                <div className='grid grid-cols-2 items-center gap-4'>
                    <PromotionInfoItem label='Promotion Goal'>
                        Drive traffic into your property
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Duration'>
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
                    </PromotionInfoItem>
                </div>
                <div className='grid grid-cols-2 gap-4'>
                    <PromotionInfoItem label='Budget'>
                        500 Php
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Payment Method'>
                        <div className='flex w-full items-center justify-between gap-4'>
                            <span>lorem ipsum</span>
                            <Button
                                variant='outline'
                                className='h-[32px] border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue/90'
                            >
                                View Receipt
                            </Button>
                        </div>
                    </PromotionInfoItem>
                </div>
                <div className='mb-8'>
                    <h1 className='text-2xl font-semibold'>
                        Promotion Activity
                    </h1>
                    <p className='text-black/50'>Analyze post performance.</p>
                </div>
                <div className='grid grid-cols-2 gap-4'>
                    <PromotionInfoItem label='Impressions'>
                        123
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Engagements'>
                        123
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Detail Expands'>
                        123
                    </PromotionInfoItem>
                    <PromotionInfoItem label='Sales'>123</PromotionInfoItem>
                </div>
            </div>
        </ScrollArea>
    );
}

ViewPromotion.layout = (page: ReactNode) => <AdminLayout>{page}</AdminLayout>;

export default ViewPromotion;
