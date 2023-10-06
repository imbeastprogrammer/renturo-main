import AdminLayout from "@/layouts/AdminLayout";
import UsersTable from "./components/UsersTable";
import dummyUsers from "@/data/dummyUsers";

function UsersPage() {
    return (
        <AdminLayout>
            <div className="rounded-lg border h-full shadow-lg gap-y-4 grid p-8 grid-rows-[auto_1fr]">
                <h1 className="text-headline-3 leading-none font-semibold">
                    Users
                </h1>
                <UsersTable users={dummyUsers} />
            </div>
        </AdminLayout>
    );
}

export default UsersPage;
