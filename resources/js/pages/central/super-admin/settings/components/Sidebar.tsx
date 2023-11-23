import { IconType } from 'react-icons';
import { Link } from '@inertiajs/react';
import { FaUser, FaKey } from 'react-icons/fa6';
import { HiBell } from 'react-icons/hi';
import { IoHelpBuoy } from 'react-icons/io5';
import { cn } from '@/lib/utils';

type SidebarItem = { label: string; path: string; icon: IconType };
const sidebarItems: SidebarItem[] = [
    {
        label: 'Account',
        path: '/super-admin/settings/account',
        icon: FaUser,
    },
    {
        label: 'Password',
        path: '/super-admin/settings/change-password',
        icon: FaKey,
    },
    {
        label: 'Notifications',
        path: '/super-admin/settings/notifications',
        icon: HiBell,
    },
    { label: 'Help', path: '/super-admin/settings/help', icon: IoHelpBuoy },
];

function Sidebar() {
    const { pathname } = window.location;

    return (
        <div className='flex flex-col gap-2 p-4'>
            {sidebarItems.map(({ label, path, icon: Icon }, i) => (
                <Link href={path} key={i}>
                    <div
                        className={cn(
                            'flex items-center gap-4 text-[15px] font-light text-black/50 transition',
                            { 'font-normal text-black': pathname === path },
                        )}
                    >
                        <Icon
                            className='h-[25px] w-[25px]'
                            color={
                                pathname === path
                                    ? '#43B3E5'
                                    : 'rgba(46, 52, 54, 0.50)'
                            }
                        />
                        {label}
                    </div>
                </Link>
            ))}
        </div>
    );
}

export default Sidebar;
