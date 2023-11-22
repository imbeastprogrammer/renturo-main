import { PropsWithChildren } from 'react';
import { GiHamburgerMenu } from 'react-icons/gi';

function CreatePromotionLayout({ children }: PropsWithChildren) {
    return (
        <div className='grid h-screen grid-rows-[114px_1fr] overflow-hidden bg-[#F9F9F9]'>
            <Header />
            <main className='grid grid-cols-[1.5fr_1fr] overflow-hidden'>
                {children}
            </main>
        </div>
    );
}

function Header() {
    return (
        <header className='p-6 shadow-lg'>
            <div className='flex h-full items-center gap-6'>
                <button type='button'>
                    <GiHamburgerMenu className='h-[40px] w-[40px] text-metalic-blue' />
                </button>
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
