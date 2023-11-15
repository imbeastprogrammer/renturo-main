import { ReactNode } from 'react';
import SuperAdminSidebar from './SuperAdminSidebar';
import Breadcrumb from './Breadcrumb';

type SuperAdminLayoutProps = {
    children: ReactNode;
};

export const LabelMap: Record<string, any> = {
    '/super-admin/settings/change-password': 'Change Password',
    '/super-admin/settings/account': 'Account Settings',
    '/super-admin/administration/user-management': 'User Management',
    '/super-admin/administration/user-management/add': 'Create User',
    '/super-admin/administration/roles': 'Roles Management',
    '/super-admin/administration/roles/edit/:id': 'Update Roles',
    '/super-admin/administration/roles/add': 'Create Role',
    '/super-admin/site-management': 'Site Management',
    '/super-admin/site-management/tenants': 'Tenants and Domains',
    '/super-admin/site-management/tenants/create': 'Add New Tenant',
    '/super-admin/site-management/tenants/edit/:id': 'Update Tenant',
};

function SuperAdminLayout({ children }: SuperAdminLayoutProps) {
    const { pathname } = window.location;

    return (
        <div className='grid h-screen grid-cols-[355px_1fr] overflow-hidden bg-[#F0F0F0] font-outfit'>
            <SuperAdminSidebar />
            <main className='grid h-full grid-rows-[auto_1fr] overflow-hidden'>
                <div className='p-4'>
                    <h1 className='text-[30px] font-semibold'>
                        {LabelMap[pathname] ||
                            (pathname.includes(
                                '/super-admin/administration/roles/edit/',
                            ) &&
                                'Update Roles') ||
                            (pathname.includes(
                                '/super-admin/administration/user-management/edit/',
                            ) &&
                                'Update User')}
                    </h1>
                    <Breadcrumb />
                </div>
                {children}
            </main>
        </div>
    );
}

export default SuperAdminLayout;
