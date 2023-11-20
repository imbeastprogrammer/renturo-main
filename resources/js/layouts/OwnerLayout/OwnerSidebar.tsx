import { ReactNode } from 'react';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import { GoTriangleDown } from 'react-icons/go';

import { sidebarItems } from './sidebar-items';
import dashboardLogo from '@/assets/dashboard-logo.png';

import { cn } from '@/lib/utils';
import { IconType } from 'react-icons';
import { IoLogOut } from 'react-icons/io5';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';

const SublinkLabelMap: Record<string, string> = {
    Post: 'Post Management',
};

function OwnerSidebar() {
    const { pathname } = window.location;

    const activeSidebarItem = sidebarItems.find((item) =>
        item.sublinks ? pathname.includes(item.path) : pathname === item.path,
    );

    const subLinks = activeSidebarItem?.sublinks;

    return (
        <aside className='grid grid-cols-[144px_1fr]'>
            <div className='grid grid-rows-[auto_1fr_auto] gap-y-8 bg-metalic-blue py-8 pl-4 pr-0'>
                <img
                    className='h-[80px]a mx-auto w-[80px] object-contain'
                    src={dashboardLogo}
                />
                <nav className='flex flex-col items-center gap-y-4'>
                    {sidebarItems.map(
                        ({ label, path, ...sidebarItem }, idx) => (
                            <SidebarLink
                                key={idx}
                                href={`/owner${path}`}
                                isActive={
                                    sidebarItem.sublinks
                                        ? pathname.includes(path)
                                        : pathname === `/owner${path}`
                                }
                                {...sidebarItem}
                            >
                                {label}
                            </SidebarLink>
                        ),
                    )}
                </nav>
                <SidebarLink icon={IoLogOut} href='/logout' method='post' />
            </div>
            {subLinks && (
                <nav className='space-y-2 border-r p-4 px-8'>
                    <h1 className='mb-4 px-4 text-[15px] font-semibold text-black/50'>
                        {SublinkLabelMap[activeSidebarItem.label] ||
                            activeSidebarItem.label}
                    </h1>
                    {subLinks.map((sublink) => {
                        const path = `${activeSidebarItem.path}${sublink.path}`;

                        if (!sublink.sublinks)
                            return (
                                <SubLink
                                    href={path}
                                    isActive={path === pathname}
                                >
                                    {sublink.label}
                                </SubLink>
                            );

                        return (
                            <Collapsible>
                                <CollapsibleTrigger className='flex w-full  items-center justify-between rounded-full p-1 px-4 text-xl font-semibold transition data-[state=open]:bg-[#EEF5FF]'>
                                    {sublink.label} <GoTriangleDown />
                                </CollapsibleTrigger>
                                <CollapsibleContent className='hidden gap-2 py-2 pl-6 data-[state=open]:grid'>
                                    {sublink.sublinks.map((childSubLink) => {
                                        const path = `${activeSidebarItem.path}${sublink.path}${childSubLink.path}`;
                                        return (
                                            <Link
                                                href={path}
                                                className={cn(
                                                    'block rounded-full px-4 py-1 text-lg font-medium',
                                                    {
                                                        'bg-[#EEF5FF]':
                                                            path === pathname,
                                                    },
                                                )}
                                            >
                                                {childSubLink.label}
                                            </Link>
                                        );
                                    })}
                                </CollapsibleContent>
                            </Collapsible>
                        );
                    })}
                </nav>
            )}
        </aside>
    );
}

type SidebarLinkProps = InertiaLinkProps & {
    children?: ReactNode;
    icon: IconType;
    isActive?: boolean;
};

function SidebarLink({ isActive = false, ...props }: SidebarLinkProps) {
    return (
        <Link
            {...props}
            className={cn(
                'relative inline-grid w-full place-items-center gap-2 rounded-l-full px-4 py-2 text-lg font-medium text-white transition',
                { 'bg-white text-metalic-blue': isActive },
            )}
        >
            {isActive && (
                <span className='absolute -top-5 left-0 h-5 w-full bg-white'>
                    <div className='absolute inset-0 rounded-br-full bg-metalic-blue'></div>
                </span>
            )}
            <props.icon className='h-[43px] w-[43px]' />
            {props.children}
            {isActive && (
                <span className='absolute -bottom-5 left-0 h-5 w-full bg-white'>
                    <div className='absolute inset-0 rounded-tr-full bg-metalic-blue'></div>
                </span>
            )}
        </Link>
    );
}

type SubLinkProps = Omit<SidebarLinkProps, 'icon'>;
function SubLink({ children, isActive, ...props }: SubLinkProps) {
    return (
        <Link
            className={cn(
                'block rounded-full p-1 px-4 text-xl font-semibold',
                isActive && 'bg-[#EEF5FF]',
            )}
            {...props}
        >
            {children}
        </Link>
    );
}

export default OwnerSidebar;
