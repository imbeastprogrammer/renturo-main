import { User } from "@/types/users";

const dummyUsers: User[] = [
    {
        no: 1,
        id: "user001",
        email: "johndoe@example.com",
        date_joined: "2023-10-01",
        name: "John Doe",
        account: "admin",
        status: "active",
    },
    {
        no: 2,
        id: "user002",
        email: "alice@example.com",
        date_joined: "2023-09-15",
        name: "Alice Smith",
        account: "owner",
        status: "offline",
    },
    {
        no: 3,
        id: "user003",
        email: "bob@example.com",
        date_joined: "2023-08-20",
        name: "Bob Johnson",
        account: "user",
        status: "active 3 days ago",
    },
    {
        no: 4,
        id: "user004",
        email: "emma@example.com",
        date_joined: "2023-07-05",
        name: "Emma Davis",
        account: "admin",
        status: "active 20 days ago",
    },
    {
        no: 5,
        id: "user005",
        email: "sophia@example.com",
        date_joined: "2023-06-10",
        name: "Sophia Wilson",
        account: "user",
        status: "offline",
    },
];

export default dummyUsers;
