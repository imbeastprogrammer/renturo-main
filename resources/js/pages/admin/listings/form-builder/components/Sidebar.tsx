import { ReactNode } from 'react';
import { InertiaLinkProps, Link, router, usePage } from '@inertiajs/react';
import {
    HomeIcon,
    LogOutIcon,
    LucideIcon,
    PlusIcon,
    SettingsIcon,
    UsersIcon,
} from 'lucide-react';

import { User } from '@/types/users';
import useMenuToggle from '../hooks/useMenuToggle';
import RenturoLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import AlluraAvatar from '@/assets/avatars/allura_avatar.png';

const sidebarItems = [
    { icon: HomeIcon, label: 'Dashboard', path: '/admin' },
    { icon: PlusIcon, label: 'Post', path: '/admin/post' },
    { icon: UsersIcon, label: 'Users', path: '/admin/users' },
    { icon: SettingsIcon, label: 'Settings', path: '/admin/settings' },
];

function Sidebar() {
    const { isOpen, toggleMenu } = useMenuToggle();
    return (
        <div className='grid h-full grid-rows-[auto_1fr_auto] bg-metalic-blue'>
            <div className='border-b border-white p-8'>
                <LogoSection />
            </div>
            <div className='space-y-4 p-8'>
                {sidebarItems.map((sidebarItem) => (
                    <SidebarItem
                        icon={sidebarItem.icon}
                        key={sidebarItem.path}
                        href={sidebarItem.path}
                        data={{ active: sidebarItem.label }}
                        onClick={() => toggleMenu(isOpen)}
                    >
                        {sidebarItem.label}
                    </SidebarItem>
                ))}
            </div>
            <div className='p-8'>
                <LogoutButton />
            </div>
        </div>
    );
}

function LogoSection() {
    const props = usePage().props;
    const auth = props.auth as { user: User };
    const user = auth.user;

    return (
        <div className='space-y-4 text-white'>
            <img
                className='mx-auto h-[39px] object-contain'
                src={RenturoLogoWhite}
            />
            <div className='mx-auto h-[118px] w-[118px] rounded-full border-4 border-white bg-[#FBF4DF]'>
                <img
                    className='h-full w-full object-contain'
                    src={AlluraAvatar}
                />
            </div>
            <h1 className='text-center text-[22px] font-semibold tracking-wide'>
                {[user.first_name, user.last_name].join(' ')}
            </h1>
        </div>
    );
}

type SidebarItemProps = {
    icon: LucideIcon;
    children: ReactNode;
} & InertiaLinkProps;

function SidebarItem({ children, ...props }: SidebarItemProps) {
    return (
        <Link className='block' {...props}>
            <div className='flex items-center gap-4 text-[18px] font-semibold text-white'>
                {props.icon && <props.icon />}
                {children}
            </div>
        </Link>
    );
}

function LogoutButton() {
    const { isOpen, toggleMenu } = useMenuToggle();
    const handleLogout = () => {
        toggleMenu(isOpen);
        router.post('/logout');
    };

    return (
        <button
            onClick={handleLogout}
            className='flex items-center gap-4 text-[18px] font-semibold text-white'
        >
            <LogOutIcon />
            Logout
        </button>
    );
}

export default Sidebar;
