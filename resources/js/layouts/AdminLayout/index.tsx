import { ReactNode } from 'react';

import AdminSidebar from './AdminSidebar';
import AdminLayoutHeader from './AdminLayoutHeader';

type AdminLayoutProps = {
    children: ReactNode;
};

export const LabelMap: Record<string, any> = {
    '/admin/post-management': 'Post Management',
    '/admin/post-management/list-of-properties': 'List of Properties',
    '/admin/post-management/bookings': 'Bookings',
    '/admin/post-management/categories': 'Categories',
    '/admin/post-management/promotions': 'Promotions',
    '/admin/post-management/ads': 'Ads',
    '/admin/user-management': 'User Management',
    '/admin/user-management/admins': 'Admins',
    '/admin/user-management/admins/create': 'Create Admin',
    '/admin/user-management/owners': 'Owners',
    '/admin/user-management/owners/create': 'Create Owner',
    '/admin/user-management/sub-owners': 'Sub Owners',
    '/admin/user-management/users': 'Users',
    '/admin/settings': 'Settings',
    '/admin/settings/personal-information': 'Personal Information',
    '/admin/settings/change-password': 'Change Password',
};

function AdminLayout({ children }: AdminLayoutProps) {
    return (
        <div className='grid h-screen grid-cols-[auto_1fr] overflow-hidden'>
            <AdminSidebar />
            <main className='grid grid-rows-[auto_1fr] gap-4 overflow-hidden p-4'>
                <AdminLayoutHeader />
                {children}
            </main>
        </div>
    );
}

export default AdminLayout;
