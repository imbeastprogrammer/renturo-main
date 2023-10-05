import AdminLayout from "@/layouts/AdminLayout";
import UsersTable from "./components/UsersTable";

function UsersPage() {
    return (
        <AdminLayout>
            <div className="rounded-lg border h-full shadow-lg gap-y-4 grid p-4 grid-rows-[auto_1fr]">
                <h1 className="text-headline-3 leading-none font-semibold">
                    Users
                </h1>
                <UsersTable />
            </div>
        </AdminLayout>
    );
}

export default UsersPage;
