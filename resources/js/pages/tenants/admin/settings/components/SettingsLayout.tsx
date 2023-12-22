import { ReactNode } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import SettingsSidebar from './SettingsSidebar';

type SettingsLayoutProps = {
    children: ReactNode;
};

function SettingsLayout({ children }: SettingsLayoutProps) {
    return (
        <AdminLayout>
            <div className='grid grid-cols-[300px_1fr] gap-4 overflow-hidden rounded-lg border bg-white shadow-lg'>
                <SettingsSidebar />
                {children}
            </div>
        </AdminLayout>
    );
}

export default SettingsLayout;
