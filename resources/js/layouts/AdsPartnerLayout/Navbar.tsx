import { ReactNode } from 'react';
import { InertiaLinkProps, Link } from '@inertiajs/react';

import dashboardLogo from '@/assets/dashboard-logo.png';

import { cn } from '@/lib/utils';
import { navbarItems } from './navbar-items';
import UserButton from './UserButton';
import Searchbar from './Searchbar';

function Navbar() {
    const { pathname } = window.location;

    return (
        <nav className='flex items-center justify-between gap-4 bg-metalic-blue p-4 px-6'>
            <div className='flex items-center gap-4'>
                <img className='h-[62px] object-contain' src={dashboardLogo} />
                {navbarItems.map(({ label, path }, i) => {
                    const fullPath = `/ads-partner${path}`;
                    const isActive = fullPath === pathname;

                    return (
                        <NavbarItem href={fullPath} key={i} isActive={isActive}>
                            {label}
                        </NavbarItem>
                    );
                })}
            </div>
            <div className='flex items-center gap-4'>
                <Searchbar placeholder='Search' />
                <UserButton />
            </div>
        </nav>
    );
}

type NavbarItemProps = InertiaLinkProps & {
    children?: ReactNode;
    isActive?: boolean;
};

function NavbarItem({ isActive = false, ...props }: NavbarItemProps) {
    return (
        <Link
            {...props}
            className={cn(
                'relative inline-grid w-full place-items-center gap-2 border-b border-transparent px-4 py-2 text-xl font-medium text-white transition',
                isActive && 'border-white',
            )}
        >
            {props.children}
        </Link>
    );
}

export default Navbar;
