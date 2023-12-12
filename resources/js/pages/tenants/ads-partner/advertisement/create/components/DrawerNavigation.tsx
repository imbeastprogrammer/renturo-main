import { IoLogOut } from 'react-icons/io5';
import { GiHamburgerMenu } from 'react-icons/gi';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';

import { navbarItems } from '@/layouts/AdsPartnerLayout/navbar-items';
import RenturoLogoWhite from '@/assets/logo/RenturoLogoWhite.png';

function DrawerNavigation() {
    return (
        <Sheet>
            <SheetTrigger>
                <GiHamburgerMenu className='h-[40px] w-[40px] text-metalic-blue' />
            </SheetTrigger>
            <SheetContent
                side='left'
                className='top-[114px] h-[calc(100%-114px)] p-0'
            >
                <div className='grid h-full grid-rows-[auto_1fr_auto] bg-metalic-blue'>
                    <div className='space-y-6 border-b p-6'>
                        <img
                            src={RenturoLogoWhite}
                            alt='app logo'
                            className='mx-auto h-[40px]'
                        />
                        <div className='space-y-4'>
                            <div className='mx-auto h-[120px] w-[120px] rounded-full bg-white'></div>
                            <div className='text-center text-white'>
                                <h1 className='text-[22px] font-semibold leading-none'>
                                    John Doe
                                </h1>
                                <span className='font-light'>Ads Partner</span>
                            </div>
                        </div>
                    </div>
                    <div className='space-y-4 p-6'>
                        {navbarItems.map(({ label, path }, idx) => {
                            const fullPath = `/ads-partner${path}`;
                            return (
                                <DrawerNavlink key={idx} href={fullPath}>
                                    {label}
                                </DrawerNavlink>
                            );
                        })}
                    </div>

                    <div className='p-6'>
                        <Link
                            method='post'
                            href='/logout'
                            className='flex items-center gap-2 text-lg font-medium text-white'
                        >
                            <IoLogOut className='h-[30px] w-[30px]' />
                            Logout
                        </Link>
                    </div>
                </div>
            </SheetContent>
        </Sheet>
    );
}

type DrawerNavlinkProps = InertiaLinkProps;
function DrawerNavlink({ children, ...props }: DrawerNavlinkProps) {
    return (
        <Link {...props} className='block text-lg font-medium text-white'>
            {children}
        </Link>
    );
}

export default DrawerNavigation;
