import { ReactNode } from "react";
import AdminSidebar from "./AdminSidebar";

type AdminLayoutProps = {
    children: ReactNode;
};

function AdminLayout({ children }: AdminLayoutProps) {
    return (
        <div className="grid h-screen grid-cols-[auto_1fr] overflow-hidden">
            <AdminSidebar />
            <main className="overflow-hidden">{children}</main>
        </div>
    );
}

export default AdminLayout;
