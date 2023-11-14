import { ReactNode } from 'react';
import { Separator } from '@/components/ui/separator';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Sidebar from './Sidebar';

type SettingsLayoutProps = {
    children: ReactNode;
};

function SettingsLayout({ children }: SettingsLayoutProps) {
    return (
        <SuperAdminLayout>
            <div className='h-full p-4'>
                <div className='grid h-full grid-cols-[250px_auto_1fr] rounded-xl border bg-white p-4 shadow-lg'>
                    <Sidebar />
                    <Separator orientation='vertical' />
                    {children}
                </div>
            </div>
        </SuperAdminLayout>
    );
}

SettingsLayout.layout = (page: ReactNode) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default SettingsLayout;
