import { Listing } from "@/types/listings";

const dummyListings: Listing[] = [
    {
        no: 1,
        id: "listing001",
        listing_name: "Cozy Apartment in Downtown",
        posted_by: "John Doe",
        price_range: "$100 - $150",
        status: "to review",
    },
    {
        no: 2,
        id: "listing002",
        listing_name: "Luxury Beachfront Villa",
        posted_by: "Alice Smith",
        price_range: "$500 - $800",
        status: "to review",
    },
    {
        no: 3,
        id: "listing003",
        listing_name: "Rustic Cabin in the Woods",
        posted_by: "Bob Johnson",
        price_range: "$50 - $75",
        status: "declined",
    },
    {
        no: 4,
        id: "listing004",
        listing_name: "Urban Loft in the City",
        posted_by: "Emma Davis",
        price_range: "$200 - $250",
        status: "posted",
    },
    {
        no: 5,
        id: "listing005",
        listing_name: "Mountain View Chalet",
        posted_by: "Sophia Wilson",
        price_range: "$300 - $400",
        status: "to review",
    },
];

export default dummyListings;
