import { ReactNode } from "react";

import AdminSidebar from "./AdminSidebar";
import AdminLayoutHeader from "./AdminLayoutHeader";

type AdminLayoutProps = {
    children: ReactNode;
};

function AdminLayout({ children }: AdminLayoutProps) {
    return (
        <div className="grid h-screen grid-cols-[auto_1fr] overflow-hidden">
            <AdminSidebar />
            <main className="overflow-hidden grid grid-rows-[auto_1fr] p-4 gap-4">
                <AdminLayoutHeader />
                {children}
            </main>
        </div>
    );
}

export default AdminLayout;
