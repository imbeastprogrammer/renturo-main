import { router } from "@inertiajs/react";

import AdminLayout from "@/layouts/AdminLayout";
import ListingsTable from "./components/ListingsTable";
import ListingFilter from "./components/ListingFilter";
import dummyListings from "@/data/dummyListings";

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
            <div className="grid grid-rows-[auto_auto_1fr] border rounded-lg shadow-lg gap-y-4 -h-full p-8">
                <div className="flex gap-4 items-end">
                    <h1 className="text-[30px] font-semibold leading-none">
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
                <ListingsTable listings={dummyListings} />
            </div>
        </AdminLayout>
    );
}

export default ListingsPage;
