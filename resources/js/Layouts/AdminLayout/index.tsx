import { ReactNode } from "react";
import AdminSidebar from "./AdminSidebar";

type AdminLayoutProps = {
    children: ReactNode;
};

function AdminLayout({ children }: AdminLayoutProps) {
    return (
        <div className="grid h-screen grid-cols-[auto_1fr]">
            <AdminSidebar />
            <main className="p-4">{children}</main>
        </div>
    );
}

export default AdminLayout;
