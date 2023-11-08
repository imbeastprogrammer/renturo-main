import { ReactNode } from 'react';
import SuperAdminSidebar from './SuperAdminSidebar';

type SuperAdminLayoutProps = {
    children: ReactNode;
};

function SuperAdminLayout({ children }: SuperAdminLayoutProps) {
    return (
        <div className='grid h-screen grid-cols-[355px_1fr] font-outfit'>
            <SuperAdminSidebar />
            {children}
        </div>
    );
}

export default SuperAdminLayout;
