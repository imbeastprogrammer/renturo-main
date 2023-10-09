import AdminLayout from "@/layouts/AdminLayout";
import UsersTable from "./components/UsersTable";
import dummyUsers from "@/data/dummyUsers";
import Pagination from "@/components/Pagination";
import { useState } from "react";

function UsersPage() {
    const [currentPage, setCurrentPage] = useState(1);
    const handleNextPage = (page: number) => setCurrentPage(page + 1);
    const handlePrevPage = (page: number) => setCurrentPage(page - 1);
    const handlePageChange = (page: number) => setCurrentPage(page);

    return (
        <AdminLayout>
            <div className="h-full gap-y-4 grid p-8 grid-rows-[auto_1fr_auto]">
                <h1 className="text-headline-3 leading-none font-semibold">
                    Users
                </h1>
                <UsersTable users={dummyUsers} />
                <Pagination
                    currentPage={currentPage}
                    numberOfPages={100}
                    onNextPage={handleNextPage}
                    onPrevPage={handlePrevPage}
                    onPageChange={handlePageChange}
                />
            </div>
        </AdminLayout>
    );
}

export default UsersPage;
