import { ReactNode } from 'react';
import { IconType } from 'react-icons';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

type SidebarLinkProps = {
    icon: IconType;
    children: ReactNode;
    isActive?: boolean;
} & InertiaLinkProps;

function SidebarLink({
    children,
    icon: Icon,
    isActive = false,
    ...props
}: SidebarLinkProps) {
    return (
        <Link
            {...props}
            className='flex items-center gap-4 text-[15px] text-white'
        >
            <div
                className={cn(
                    'absolute -left-4 h-[40px] w-1 rounded-lg bg-white/50 opacity-0 transition',
                    isActive && 'opacity-100',
                )}
            />
            <Icon className='h-[30px] w-[30px]' />
            {children}
        </Link>
    );
}

export default SidebarLink;
