import { Category } from "@/types/categories";

const dummyCategories: Category[] = [
    {
        no: 1,
        id: "category001",
        category_name: "Electronics",
        parent: "None",
        status: "approved",
    },
    {
        no: 2,
        id: "category002",
        category_name: "Clothing",
        parent: "None",
        status: "to review",
    },
    {
        no: 3,
        id: "category003",
        category_name: "Home & Garden",
        parent: "None",
        status: "declined",
    },
    {
        no: 4,
        id: "category004",
        category_name: "Books",
        parent: "None",
        status: "approved",
    },
    {
        no: 5,
        id: "category005",
        category_name: "Sports & Outdoors",
        parent: "None",
        status: "to review",
    },
    {
        no: 6,
        id: "category006",
        category_name: "Automotive",
        parent: "None",
        status: "declined",
    },
];

export default dummyCategories;
