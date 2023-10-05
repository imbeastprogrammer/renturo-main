import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { TrashIcon } from "lucide-react";

const statusColor: Record<string, string> = {
    active: "#84C58A",
    offline: "#EB4F4F",
};

function UsersTable() {
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
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Joshua dela cruz</TableCell>
                    <TableCell>delacruzjoshua691@gmail.com</TableCell>
                    <TableCell>October 1, 2023</TableCell>
                    <TableCell>Business</TableCell>
                    <TableCell>
                        <span style={{ color: statusColor["active"] }}>
                            Active
                        </span>
                    </TableCell>
                    <TableCell>
                        <TrashIcon />
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Joshua dela cruz</TableCell>
                    <TableCell>delacruzjoshua691@gmail.com</TableCell>
                    <TableCell>October 1, 2023</TableCell>
                    <TableCell>Business</TableCell>
                    <TableCell>
                        <span style={{ color: statusColor["offline"] }}>
                            Offline
                        </span>
                    </TableCell>
                    <TableCell>
                        <TrashIcon />
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className="font-medium">1</TableCell>
                    <TableCell>LI-000-001</TableCell>
                    <TableCell>Joshua dela cruz</TableCell>
                    <TableCell>delacruzjoshua691@gmail.com</TableCell>
                    <TableCell>October 1, 2023</TableCell>
                    <TableCell>Business</TableCell>
                    <TableCell>
                        <span
                            style={{
                                color: statusColor["sample-status"] || "gray",
                            }}
                        >
                            Active 30 Days ago
                        </span>
                    </TableCell>
                    <TableCell>
                        <TrashIcon />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}

export default UsersTable;
