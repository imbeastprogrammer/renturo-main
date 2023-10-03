import { ReactNode } from "react";
import DashboardSidebar from "./DashboardSidebar";

type DashboardLayoutProps = {
    children: ReactNode;
};

function DashboardLayout({ children }: DashboardLayoutProps) {
    return (
        <div className="grid h-screen grid-cols-[auto_1fr]">
            <DashboardSidebar />
            <main className="p-4">{children}</main>
        </div>
    );
}

export default DashboardLayout;
