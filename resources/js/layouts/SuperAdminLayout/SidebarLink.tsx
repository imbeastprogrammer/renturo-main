import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

type SidebarLinkProps = {
    icon: string;
    children: ReactNode;
    isActive: boolean;
    path: string;
};

function SidebarLink({ children, icon, isActive, path }: SidebarLinkProps) {
    return (
        <Link
            href={path}
            className='flex items-center gap-4 text-[15px] text-white'
        >
            {isActive && (
                <div className='absolute -left-4 h-[40px] w-1 rounded-lg bg-white/50' />
            )}
            <img
                src={icon}
                alt='navlink icon'
                className='h-[30px] w-[30px] object-contain'
            />
            {children}
        </Link>
    );
}

export default SidebarLink;
