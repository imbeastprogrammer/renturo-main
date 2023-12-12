import { PropsWithChildren } from 'react';
import DrawerNavigation from './DrawerNavigation';

function CreatePromotionLayout({ children }: PropsWithChildren) {
    return (
        <div className='grid h-screen grid-rows-[114px_1fr] overflow-hidden bg-[#F9F9F9]'>
            <Header />
            <main className='grid grid-cols-[1.2fr_1fr] overflow-hidden'>
                {children}
            </main>
        </div>
    );
}

function Header() {
    return (
        <header className='z-[1000] p-6 shadow-lg'>
            <div className='flex h-full items-center gap-6'>
                <DrawerNavigation />
                <div>
                    <h1 className='text-[30px] font-semibold leading-none'>
                        Create Promotion
                    </h1>
                    <p className='text-xl font-medium text-black/50'>
                        Promote you post, your way!
                    </p>
                </div>
            </div>
        </header>
    );
}

export default CreatePromotionLayout;
