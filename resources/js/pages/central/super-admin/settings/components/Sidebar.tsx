import { Link } from '@inertiajs/react';
import {
    AccountIcon,
    HelpIcon,
    NotificationsIcon,
    PasswordIcon,
} from '@/assets/central/sidebar';
import { cn } from '@/lib/utils';

const sidebarItems = [
    {
        label: 'Account',
        path: '/super-admin/settings/account',
        icon: AccountIcon,
    },
    {
        label: 'Password',
        path: '/super-admin/settings/change-password',
        icon: PasswordIcon,
    },
    {
        label: 'Notifications',
        path: '/super-admin/settings/notifications',
        icon: NotificationsIcon,
    },
    { label: 'Help', path: '/super-admin/settings/help', icon: HelpIcon },
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
