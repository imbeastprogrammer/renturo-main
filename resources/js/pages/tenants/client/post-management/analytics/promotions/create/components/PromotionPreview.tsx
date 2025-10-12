import { Button } from '@/components/ui/button';

function PromotionPreview() {
    return (
        <div className='space-y-4 p-6'>
            <h1 className='text-xl font-semibold text-black/50'>Preview</h1>
            <PromotionPreviewCard />
        </div>
    );
}

function PromotionPreviewCard() {
    return (
        <div className='space-y-4 rounded-lg border bg-white p-4'>
            <div className='flex items-center gap-4'>
                <div className='h-[48px] w-[48px] flex-shrink-0 rounded-full bg-red-500'></div>
                <div>
                    <h2 className='text-base font-medium leading-none'>KFC</h2>
                    <p className='text-xs font-light'>Sponsored</p>
                </div>
            </div>
            <div className='h-[222px] rounded-lg bg-red-500'></div>
            <div className='flex gap-4'>
                <p className='text-[17px]'>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
                    do eiusmod tempor incididunt ut labore et.
                </p>
                <Button className='flex-shrink-0 bg-metalic-blue text-base font-medium hover:bg-metalic-blue/90'>
                    Learn More
                </Button>
            </div>
        </div>
    );
}

export default PromotionPreview;
