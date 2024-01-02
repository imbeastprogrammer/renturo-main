import { ReactNode } from 'react';
import { IconType } from 'react-icons';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/react';
import { BiMessageSquareAdd } from 'react-icons/bi';
import { AiFillHome } from 'react-icons/ai';
import { FaUsers } from 'react-icons/fa';
import { FiUser } from 'react-icons/fi';
import { IoLogOut } from 'react-icons/io5';

import { User } from '@/types/users';
import useMenuToggle from '../hooks/useMenuToggle';
import RenturoLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import AlluraAvatar from '@/assets/avatars/allura_avatar.png';

const sidebarItems = [
    { icon: AiFillHome, label: 'Dashboard', path: '/admin' },
    {
        icon: BiMessageSquareAdd,
        label: 'Post',
        path: '/admin/post-management/properties',
    },
    { icon: FaUsers, label: 'Users', path: '/admin/user-management/users' },
    {
        icon: FiUser,
        label: 'Settings',
        path: '/admin/settings/personal-information',
    },
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
                        onClick={() => toggleMenu(isOpen)}
                    >
                        {sidebarItem.label}
                    </SidebarItem>
                ))}
            </div>
            <div className='p-8'>
                <SidebarItem
                    icon={IoLogOut}
                    href='/logout'
                    method='post'
                    onClick={() => toggleMenu(isOpen)}
                >
                    Logout
                </SidebarItem>
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
    icon: IconType;
    children: ReactNode;
} & InertiaLinkProps;

function SidebarItem({ children, icon: Icon, ...props }: SidebarItemProps) {
    return (
        <Link className='block' {...props}>
            <div className='flex items-center gap-4 text-lg font-semibold text-white'>
                {Icon && <Icon className='h-[30px] w-[30px]' />}
                {children}
            </div>
        </Link>
    );
}

export default Sidebar;
