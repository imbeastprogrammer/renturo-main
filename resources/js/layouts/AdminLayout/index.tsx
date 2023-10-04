import { ReactNode } from "react";
import AdminSidebar from "./AdminSidebar";

type AdminLayoutProps = {
    children: ReactNode;
};

function AdminLayout({ children }: AdminLayoutProps) {
    return (
        <div className="grid h-screen p-4 gap-4 grid-cols-[auto_1fr]">
            <AdminSidebar />
            <main>{children}</main>
        </div>
    );
}

export default AdminLayout;
