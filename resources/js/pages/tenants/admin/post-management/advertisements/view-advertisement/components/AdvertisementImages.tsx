import { Button } from '@/components/ui/button';

function PromotionImages() {
    return (
        <div className='flex items-center gap-4'>
            <span className='text-xl'>Images</span>
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

export default PromotionImages;
