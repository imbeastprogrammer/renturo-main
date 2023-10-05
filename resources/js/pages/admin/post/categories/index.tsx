import AdminLayout from "@/layouts/AdminLayout";
import ListingFilter from "../listings/components/ListingFilter";
import { router } from "@inertiajs/react";
import CategoriesTable from "./components/CategoriesTable";

const tabs = [
    { label: "All Categories", value: "all" },
    { label: "Approved", value: "approved" },
    { label: "To Review", value: "review" },
    { label: "Declined", value: "declined" },
];

function CategoriesPage() {
    const searchParams = new URLSearchParams(window.location.search);
    const filter = searchParams.get("filter") || "all";

    const handleChangeFilter = (value: string) => {
        router.visit(`/admin/post/categories?active=Post&filter=${value}`);
    };

    return (
        <AdminLayout>
            <div className="h-full grid grid-rows-[auto_auto_1fr] gap-y-4 p-4 rounded-lg border shadow-lg">
                <h1 className="text-headline-3 leading-none font-semibold">
                    Categories
                </h1>
                <ListingFilter
                    value={filter}
                    data={tabs}
                    onChange={handleChangeFilter}
                />
                <CategoriesTable />
            </div>
        </AdminLayout>
    );
}

export default CategoriesPage;
