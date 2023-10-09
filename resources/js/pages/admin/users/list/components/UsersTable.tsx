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

// const statusColor: Record<string, string> = {
//     active: "#84C58A",
//     offline: "#EB4F4F",
// };

type UsersTableProps = {
    users: User[];
};

function UsersTable({ users = [] }: UsersTableProps) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead className="w-[100px]">Id</TableHead>
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
                    <TableRow key={user.id}>
                        <TableCell className="font-medium">{user.id}</TableCell>
                        <TableCell>
                            {[user.first_name, user.last_name].join(" ")}
                        </TableCell>
                        <TableCell>{user.email}</TableCell>
                        <TableCell>NA for now</TableCell>
                        <TableCell>{user.role}</TableCell>
                        <TableCell>
                            {/* <span
                                className="capitalize"
                                style={{ color: statusColor[user.status] }}
                            >
                                {user.status}

                            </span> */}
                            NA for now
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
