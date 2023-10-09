import { useState } from "react";
import AdminLayout from "@/layouts/AdminLayout";
import UsersTable from "./components/UsersTable";
import dummyUsers from "@/data/dummyUsers";
import Pagination from "@/components/Pagination";

function UsersPage() {
    const [currentPage, setCurrentPage] = useState(1);
    const handleNextPage = (page: number) => setCurrentPage(page + 1);
    const handlePrevPage = (page: number) => setCurrentPage(page - 1);
    const handlePageChange = (page: number) => setCurrentPage(page);

    return (
        <AdminLayout>
            <div className="h-full gap-y-4 grid p-8 border rounded-lg shadow-lg grid-rows-[auto_auto_1fr_auto]">
                <p className="text-[15px] text-gray-500">
                    Users / User Management / List of Users
                </p>
                <h1 className="text-[30px] leading-none font-semibold">
                    List of Users
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
