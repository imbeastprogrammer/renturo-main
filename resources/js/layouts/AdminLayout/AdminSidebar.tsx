import { ReactNode } from 'react';
import { Link, InertiaLinkProps } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { IconType } from 'react-icons';
import { IoLogOut } from 'react-icons/io5';
import { FiUser } from 'react-icons/fi';

import { sidebarItems } from './sidebar-items';
import dashboardLogo from '@/assets/dashboard-logo.png';

const GroupLinkLabelMap: Record<string, string> = {
    Users: 'User Management',
    Post: 'Post Mangement',
};

type SidebarLinkProps = InertiaLinkProps & {
    icon: IconType;
    children?: ReactNode;
    isActive: boolean;
};

function SidebarLink({ isActive, icon: Icon, ...props }: SidebarLinkProps) {
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
            {Icon && <Icon className='h-[43px] w-[43px]' />}
            {props.children}
            {isActive && (
                <span className='absolute -bottom-5 left-0 h-5 w-full bg-white'>
                    <div className='absolute inset-0 rounded-tr-full bg-metalic-blue'></div>
                </span>
            )}
        </Link>
    );
}

function SecondaryLink({
    isActive,
    ...props
}: Omit<SidebarLinkProps, 'icon' | 'label'>) {
    return (
        <Link
            {...props}
            className={cn(
                'inline-block rounded-full p-2 px-4 font-semibold transition',
                {
                    'bg-blue-50': isActive,
                },
            )}
        >
            {props.children}
        </Link>
    );
}

function AdminSidebar() {
    const { pathname } = window.location;

    const activeSidebarItem = sidebarItems.find((item) =>
        item.sublinks ? pathname.includes(item.path) : pathname === item.path,
    );
    const subLinks = activeSidebarItem?.sublinks;

    return (
        <aside className='h-full'>
            <div className='grid h-full grid-cols-[144px_1fr]'>
                <div className='grid h-full grid-rows-[1fr_auto] bg-metalic-blue px-4 py-8 pr-0 text-white'>
                    <div>
                        <img
                            className='mx-auto h-[80px] w-[80px] object-contain'
                            src={dashboardLogo}
                        />
                        <nav>
                            <ul className='mt-6 space-y-4'>
                                {sidebarItems.map((sidebarItem, i) => {
                                    const path = sidebarItem.sublinks
                                        ? `/admin${sidebarItem.path}${sidebarItem.sublinks[0].path}`
                                        : `/admin${sidebarItem.path}`;

                                    const isActive = sidebarItem.sublinks
                                        ? pathname.includes(sidebarItem.path)
                                        : pathname ===
                                          `/admin${sidebarItem.path}`;

                                    return (
                                        <li key={i}>
                                            <SidebarLink
                                                icon={sidebarItem.icon}
                                                href={path}
                                                isActive={isActive}
                                            >
                                                {sidebarItem.label}
                                            </SidebarLink>
                                        </li>
                                    );
                                })}
                                <li>
                                    <SidebarLink
                                        icon={FiUser}
                                        href='/admin/settings/personal-infomration'
                                        isActive={pathname.includes('settings')}
                                    >
                                        Settings
                                    </SidebarLink>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <SidebarLink
                        href='/logout'
                        method='post'
                        icon={IoLogOut}
                        isActive={false}
                    />
                </div>
                {activeSidebarItem?.sublinks && (
                    <nav className='flex w-[250px] flex-col gap-2 border-r p-4'>
                        <h1 className='mb-4 px-4 text-[15px] font-semibold text-black/50'>
                            {GroupLinkLabelMap[activeSidebarItem?.label] ||
                                activeSidebarItem?.label}
                        </h1>
                        {subLinks?.map((link, idx) => {
                            const path = `/admin${activeSidebarItem.path}${link.path}`;

                            return (
                                <SecondaryLink
                                    isActive={pathname.includes(link.path)}
                                    key={idx}
                                    href={path}
                                >
                                    {link.label}
                                </SecondaryLink>
                            );
                        })}
                    </nav>
                )}
            </div>
        </aside>
    );
}

export default AdminSidebar;
