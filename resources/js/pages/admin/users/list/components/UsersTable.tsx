import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { User } from "@/types/users";
import { TrashIcon } from "lucide-react";

const statusColor: Record<string, string> = {
    active: "#84C58A",
    offline: "#EB4F4F",
};

type UsersTableProps = {
    users: User[];
};

function UsersTable({ users = [] }: UsersTableProps) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[100px]">#</TableHead>
                    <TableHead>Id</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Date Joined</TableHead>
                    <TableHead>Account</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="w-[50px]"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {users.map((user) => (
                    <TableRow key={user.no}>
                        <TableCell className="font-medium">{user.no}</TableCell>
                        <TableCell>{user.id}</TableCell>
                        <TableCell>{user.name}</TableCell>
                        <TableCell>{user.email}</TableCell>
                        <TableCell>{user.date_joined}</TableCell>
                        <TableCell>{user.account}</TableCell>
                        <TableCell>
                            <span
                                className="capitalize"
                                style={{ color: statusColor[user.status] }}
                            >
                                {user.status}
                            </span>
                        </TableCell>
                        <TableCell>
                            <TrashIcon className="text-red-500" />
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default UsersTable;
