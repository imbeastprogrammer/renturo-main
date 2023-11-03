import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { PropertyListing } from '@/types/properties';

type UsersTableProps = {
    properties: PropertyListing[];
};

const StatusColorMap: Record<string, string> = {
    active: 'text-green-500',
    offline: 'text-red-500',
};

function PropertListingsTable({ properties = [] }: UsersTableProps) {
    return (
        <Table>
            <TableHeader className='sticky top-0 bg-white'>
                <TableRow>
                    <TableHead className='w-[100px]'>Id</TableHead>
                    <TableHead>Category</TableHead>
                    <TableHead>Subcategory</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Status</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {properties.map((property) => (
                    <TableRow key={property.id}>
                        <TableCell className='font-medium'>
                            {property.id}
                        </TableCell>
                        <TableCell>{property.category}</TableCell>
                        <TableCell>{property.subcategory}</TableCell>
                        <TableCell>{property.name}</TableCell>
                        <TableCell>{property.date}</TableCell>
                        <TableCell
                            className={cn(
                                'capitalize',
                                StatusColorMap[property.status] ||
                                    'text-gray-500',
                            )}
                        >
                            {property.status}
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export default PropertListingsTable;
