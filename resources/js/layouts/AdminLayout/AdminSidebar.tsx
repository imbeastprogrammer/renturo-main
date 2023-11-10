import { Link, InertiaLinkProps } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import {
    Building2Icon,
    HomeIcon,
    LucideIcon,
    PlusIcon,
    SettingsIcon,
    UsersIcon,
    XIcon,
} from 'lucide-react';

import dashboardLogo from '@/assets/dashboard-logo.png';
import LogoutButton from './LogoutButton';
import { useSearchParams } from '@/hooks/useSearchParams';

const dashboardLinks = [
    { label: 'Dashboard', to: '/admin', icon: HomeIcon, links: [] },
    {
        label: 'Post',
        to: '/admin/post',
        icon: PlusIcon,
        links: [
            { label: 'Listings', to: '/admin/post' },
            { label: 'Bookings', to: '/admin/post/bookings' },
            { label: 'Categories', to: '/admin/post/categories' },
        ],
    },
    {
        label: 'Users',
        to: '/admin/users',
        icon: UsersIcon,
        links: [
            { label: 'Admin', to: '/admin/user-management/admin' },
            { label: 'Owners', to: '/admin/user-management/owners' },
            { label: 'Sub Owners', to: '/admin/user-management/sub-owners' },
            { label: 'Users', to: '/admin/user-management/users' },
        ],
    },
    {
        label: 'Listings',
        to: '/admin/listings',
        icon: Building2Icon,
        links: [
            { label: 'List of Properties', to: '/admin/listings' },
            { label: 'For Approval', to: '/admin/listings/for-approval' },
            { label: 'Form Builder', to: '/admin/listings/form-builder' },
        ],
    },
    {
        label: 'Settings',
        to: '/admin/settings',
        icon: SettingsIcon,
        links: [],
    },
];

const GroupLinkLabelMap: Record<string, string> = {
    Users: 'User Management',
};

type SidebarLinkProps = InertiaLinkProps & {
    icon: LucideIcon;
    label: string;
    isActive: boolean;
};

function SidebarLink({ isActive, ...props }: SidebarLinkProps) {
    return (
        <Link
            {...props}
            className={cn(
                'relative inline-grid w-full place-items-center gap-2 rounded-l-full p-2 text-[15px] transition',
                { 'bg-white text-metalic-blue': isActive },
            )}
        >
            {isActive && (
                <span className='absolute -top-5 left-0 h-5 w-full bg-white'>
                    <div className='absolute inset-0 rounded-br-full bg-metalic-blue'></div>
                </span>
            )}
            <props.icon className='h-[43px] w-[43px]' />
            {props.label}
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
    const { searchParams, queryParams } = useSearchParams();
    const activeLink = searchParams.get('active') || 'Dashboard';

    const activeLinkChildrenLinks = dashboardLinks.find(
        (link) => link.label === activeLink,
    );

    const displaySubLinks = (activeLinkChildrenLinks?.links.length || 0) > 0;

    return (
        <aside className='h-full'>
            <div className='flex h-full'>
                <div className='grid h-full w-[130px] grid-rows-[1fr_auto] bg-metalic-blue px-4 py-8 pr-0 text-white'>
                    <div>
                        <img
                            className='h-[80px]a mx-auto w-[80px] object-contain'
                            src={dashboardLogo}
                        />
                        <nav>
                            <ul className='mt-6 space-y-4'>
                                {dashboardLinks.map((link, i) => (
                                    <li key={i}>
                                        <SidebarLink
                                            icon={link.icon}
                                            href={link.to}
                                            data={{ active: link.label }}
                                            label={link.label}
                                            isActive={activeLink === link.label}
                                        />
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </div>
                    <LogoutButton />
                </div>
                {displaySubLinks && (
                    <nav className='flex w-[250px] flex-col gap-2 border-r p-4'>
                        <h1 className='mb-4 px-4 text-[15px] font-semibold text-black/50'>
                            {(activeLinkChildrenLinks &&
                                GroupLinkLabelMap[
                                    activeLinkChildrenLinks?.label
                                ]) ||
                                activeLinkChildrenLinks?.label}
                        </h1>
                        {activeLinkChildrenLinks?.links.map((link) => (
                            <SecondaryLink
                                isActive={link.to === pathname}
                                key={link.to}
                                href={link.to}
                                data={{ ...queryParams }}
                            >
                                {link.label}
                            </SecondaryLink>
                        ))}
                    </nav>
                )}
            </div>
        </aside>
    );
}

export default AdminSidebar;
