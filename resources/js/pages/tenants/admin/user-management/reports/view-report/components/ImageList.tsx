import { Button } from '@/components/ui/button';

function ImageList() {
    return (
        <div className='flex items-center gap-4'>
            <h2 className='text-lg font-medium'>Screenshot(s)</h2>
            <div className='grid grid-cols-5 items-center gap-4'>
                <div className='h-[158px] bg-metalic-blue'></div>
                <div className='h-[158px] bg-metalic-blue'></div>
                <div className='h-[158px] bg-metalic-blue'></div>
                <div className='h-[158px] bg-metalic-blue'></div>
                <Button
                    variant='outline'
                    className='h-[28px] border-metalic-blue text-[13px] text-metalic-blue hover:border-metalic-blue/90 hover:bg-metalic-blue/5 hover:text-metalic-blue/90'
                >
                    Preview
                </Button>
            </div>
        </div>
    );
}

export default ImageList;
