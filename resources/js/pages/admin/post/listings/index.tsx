import { router } from "@inertiajs/react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

import AdminLayout from "@/layouts/AdminLayout";
import AllListingsTable from "./components/AllListingsTable";
import PostedListingsTable from "./components/PostedListingsTable";
import ToReviewListingsTable from "./components/ToReviewListingsTable";
import DeclinedListingsTable from "./components/DeclinedListingsTable";

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
            <div className="bg-pure-white border shadow-lg grid grid-rows-[auto_1fr] gap-y-8 rounded-lg h-full p-4">
                <div className="flex gap-4 items-end">
                    <h1 className="text-headline-3 font-semibold leading-none">
                        Listings
                    </h1>
                    <span className="text-[15px] text-gray-500 font-semibold">
                        3 Listings found
                    </span>
                </div>
                <Tabs
                    value={filter || "all"}
                    onValueChange={(value) => {
                        router.visit(`/admin/post?active=Post&filter=${value}`);
                    }}
                >
                    <TabsList className="bg-white gap-4">
                        {tabs.map((tab) => (
                            <TabsTrigger
                                key={tab.value}
                                value={tab.value}
                                className="text-headline-4 text-heavy-carbon transition border-b border-transparent data-[state=active]:shadow-none data-[state=active]:border-metalic-blue data-[state=active]:text-metalic-blue"
                            >
                                {tab.label}
                            </TabsTrigger>
                        ))}
                    </TabsList>
                    <TabsContent value="all" className="pt-4">
                        <AllListingsTable />
                    </TabsContent>
                    <TabsContent value="posted">
                        <PostedListingsTable />
                    </TabsContent>
                    <TabsContent value="review">
                        <ToReviewListingsTable />
                    </TabsContent>
                    <TabsContent value="declined">
                        <DeclinedListingsTable />
                    </TabsContent>
                </Tabs>
            </div>
        </AdminLayout>
    );
}

export default ListingsPage;
