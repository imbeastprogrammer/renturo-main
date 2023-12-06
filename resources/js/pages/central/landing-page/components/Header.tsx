import { PropsWithChildren, useState } from 'react';
import { Link, LinkProps } from 'react-scroll';
import { FiMenu } from 'react-icons/fi';
import {
    Sheet,
    SheetContent,
    SheetTrigger,
    SheetClose,
} from '@/components/ui/sheet';

import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoBlue.png';
import RenturoTextLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import { cn } from '@/lib/utils';

const navlinks = [
    { to: 'home', label: 'Home' },
    { to: 'about', label: 'About Us' },
    { to: 'download', label: 'Download' },
    { to: 'contact', label: 'Contact Us' },
];

function Header() {
    return (
        <header className='sticky top-0 z-[1000] bg-white'>
            <div className='mx-auto flex max-w-screen-lg items-center justify-between p-4 2xl:max-w-screen-xl 3xl:max-w-screen-2xl'>
                <div>
                    <img
                        src={RenturoTextLogoBlue}
                        alt='app logo'
                        className='h-[36px] 2xl:h-[74px]'
                    />
                </div>
                <ul className='hidden cursor-pointer gap-8 font-medium md:text-xl xl:flex 2xl:text-[25px]'>
                    <NavLink>Home</NavLink>
                    <NavLink>About Us</NavLink>
                    <NavLink>Download</NavLink>
                    <NavLink>Contact Us</NavLink>
                </ul>
                <button className='hidden h-[40px] w-[170px] rounded-lg bg-metalic-blue  text-xl font-bold text-white lg:block 2xl:h-[60px] 2xl:w-[228px] 2xl:text-[25px]'>
                    Get Started
                </button>
                <MobileNavigation />
            </div>
        </header>
    );
}

type NavLinkProps = PropsWithChildren;
function NavLink({ children }: NavLinkProps) {
    return <li className='font-medium text-black/70 transition'>{children}</li>;
}

function MobileNavigation() {
    const [open, setOpen] = useState(false);

    return (
        <Sheet open={open} onOpenChange={setOpen}>
            <SheetTrigger className='md:hidden'>
                <FiMenu className='h-[30px] w-[30px] text-metalic-blue' />
            </SheetTrigger>
            <SheetContent className='z-[1000] w-full bg-metalic-blue text-white'>
                <div className='grid grid-rows-[auto_1fr_auto] gap-y-8'>
                    <div className='flex items-center justify-between'>
                        <div>
                            <img
                                src={RenturoTextLogoWhite}
                                alt='app logo'
                                className='h-[36px] 2xl:h-[74px]'
                            />
                        </div>
                        <SheetClose>
                            <FiMenu className='h-[30px] w-[30px] text-white' />
                        </SheetClose>
                    </div>
                    <div>
                        <ul className='grid gap-2 font-medium'>
                            {navlinks.map(({ to, label }, i) => (
                                <MobileNavLink
                                    to={to}
                                    key={i}
                                    onClick={() => setOpen(false)}
                                >
                                    {label}
                                </MobileNavLink>
                            ))}
                        </ul>
                    </div>
                    <div></div>
                </div>
            </SheetContent>
        </Sheet>
    );
}

type MobileNavLinkProps = LinkProps;
function MobileNavLink({
    children,
    className,
    to,
    onClick,
}: MobileNavLinkProps) {
    return (
        <Link
            to={to}
            smooth
            spy
            activeClass='border-white'
            offset={-100}
            className={cn(
                'w-max border-b border-transparent pb-4 text-[15px]',
                className,
            )}
            onClick={onClick}
        >
            {children}
        </Link>
    );
}

export default Header;
