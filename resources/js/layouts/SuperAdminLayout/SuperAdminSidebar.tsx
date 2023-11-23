import { RiDatabase2Fill } from 'react-icons/ri';
import { BiSolidLayout } from 'react-icons/bi';
import { FaUsers } from 'react-icons/fa';
import { IoSettingsSharp } from 'react-icons/io5';
import { IoLogOut } from 'react-icons/io5';
import { Separator } from '@/components/ui/separator';

import KabootekTextLogoWhite from '@/assets/central/auth/kabootek-text-logo-white.png';
import GroupLinks from './GroupLinks';
import SidebarLink from './SidebarLink';

function SuperAdminSidebar() {
    const { pathname } = window.location;

    return (
        <div className='bg-yinmn-blue'>
            <div className='p-4'>
                <img
                    src={KabootekTextLogoWhite}
                    alt='logo'
                    className='h-[30px]'
                />
            </div>
            <div className='space-y-4 p-4'>
                <h1 className='text-[15px] font-light uppercase text-white/50'>
                    Menu
                </h1>
                <div className='relative space-y-2'>
                    <SidebarLink
                        isActive={pathname === '/super-admin'}
                        icon={BiSolidLayout}
                        href='/super-admin'
                    >
                        Dashboard
                    </SidebarLink>
                    <GroupLinks
                        isActive={pathname.includes('/administration')}
                        label='Administration'
                        icon={FaUsers}
                        links={[
                            {
                                label: 'User Management',
                                path: '/super-admin/administration/user-management',
                            },
                            {
                                label: 'Roles',
                                path: '/super-admin/administration/roles',
                            },
                            {
                                label: 'Add User',
                                path: '/super-admin/administration/user-management/add',
                            },
                        ]}
                    />
                    <GroupLinks
                        isActive={pathname.includes('/site-management')}
                        label='Site Management'
                        icon={RiDatabase2Fill}
                        links={[
                            {
                                label: 'Tenants and Domains',
                                path: '/super-admin/site-management/tenants',
                            },
                        ]}
                    />
                </div>
            </div>
            <div className='p-4'>
                <Separator className='bg-white/50' />
            </div>
            <div className='space-y-4 p-4'>
                <h1 className='text-[15px] uppercase text-white/50'>Other</h1>
                <div className='relative space-y-4'>
                    <SidebarLink
                        href='/super-admin/settings/account'
                        isActive={pathname === '/super-admin/settings/account'}
                        icon={IoSettingsSharp}
                    >
                        Settings
                    </SidebarLink>
                    <SidebarLink method='post' href='/logout' icon={IoLogOut}>
                        Logout
                    </SidebarLink>
                </div>
            </div>
        </div>
    );
}

export default SuperAdminSidebar;
