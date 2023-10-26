import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { CloudIcon, MenuIcon } from 'lucide-react';

function FormBuilderHeader() {
    return (
        <header className='flex h-[114px] items-center justify-between shadow-lg'>
            <div className='flex h-full items-center gap-6 p-4'>
                <div>
                    <Button
                        variant='ghost'
                        size='icon'
                        className='h-[70px] w-[70px] text-metalic-blue'
                    >
                        <MenuIcon className='h-[50px] w-[50px]' />
                    </Button>
                </div>
                <div>
                    <h1 className='text-[30px] font-semibold leading-none'>
                        Form Builder
                    </h1>
                    <p className='text-[20px] text-gray-500'>
                        Add and customize forms
                    </p>
                </div>
                <Separator orientation='vertical' className='w-1' />
                <div>
                    <CloudIcon className='h-[40px] w-[40px]' />
                </div>
            </div>
            <div className='flex gap-2 p-4'>
                <Button variant='outline' className='w-36 text-metalic-blue'>
                    Save
                </Button>
                <Button className='w-36 bg-metalic-blue hover:bg-metalic-blue/90'>
                    Publish
                </Button>
            </div>
        </header>
    );
}

export default FormBuilderHeader;
