import { router } from "@inertiajs/react";
import AdminLayout from "@/layouts/AdminLayout";
import ListingFilter from "../listings/components/ListingFilter";
import BookingsTable from "./components/BookingsTable";
import dummyBookings from "@/data/dummyBookings";

const tabs = [
    { label: "All Bookings", value: "all" },
    { label: "Done", value: "done" },
    { label: "Upcoming", value: "upcoming" },
    { label: "Canceled", value: "canceled" },
];

function BookingsPage() {
    const searchParams = new URLSearchParams(window.location.search);
    const filter = searchParams.get("filter");

    return (
        <AdminLayout>
            <div className="h-full border rounded-lg grid grid-rows-[auto_auto_1fr] gap-y-4 shadow-lg p-8">
                <div>
                    <h1 className="text-headline-3 font-semibold leading-none">
                        Bookings
                    </h1>
                </div>
                <ListingFilter
                    value={filter || "all"}
                    data={tabs}
                    onChange={(value) => {
                        router.visit(
                            `/admin/post/bookings?active=Post&filter=${value}`
                        );
                    }}
                />
                <BookingsTable bookings={dummyBookings} />
            </div>
        </AdminLayout>
    );
}

export default BookingsPage;
