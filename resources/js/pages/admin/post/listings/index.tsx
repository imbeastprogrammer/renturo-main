import { router } from "@inertiajs/react";

import AdminLayout from "@/layouts/AdminLayout";
import ListingsTable from "./components/ListingsTable";
import ListingFilter from "./components/ListingFilter";

const tabs = [
    { label: "All Listings", value: "all" },
    { label: "Posted", value: "posted" },
    { label: "To Review", value: "review" },
    { label: "Declined", value: "declined" },
];

function ListingsPage() {
    const searchParams = new URLSearchParams(window.location.search);
    const filter = searchParams.get("filter");

    return (
        <AdminLayout>
            <div className="bg-pure-white border shadow-lg grid grid-rows-[auto_auto_1fr] gap-y-8 rounded-lg h-full p-4">
                <div className="flex gap-4 items-end">
                    <h1 className="text-headline-3 font-semibold leading-none">
                        Listings
                    </h1>
                    <span className="text-[15px] text-gray-500 font-semibold">
                        3 Listings found
                    </span>
                </div>
                <ListingFilter
                    value={filter || "all"}
                    data={tabs}
                    onChange={(value) => {
                        router.visit(`/admin/post?active=Post&filter=${value}`);
                    }}
                />
                <ListingsTable />
            </div>
        </AdminLayout>
    );
}

export default ListingsPage;
