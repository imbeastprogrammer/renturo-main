import { ReactNode } from 'react';
import SuperAdminSidebar from './SuperAdminSidebar';

type SuperAdminLayoutProps = {
    children: ReactNode;
};

function SuperAdminLayout({ children }: SuperAdminLayoutProps) {
    return (
        <div className='grid h-screen grid-cols-[355px_1fr] overflow-hidden bg-[#F0F0F0] font-outfit'>
            <SuperAdminSidebar />
            <main className='h-full overflow-hidden'>{children}</main>
        </div>
    );
}

export default SuperAdminLayout;
