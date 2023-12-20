import { useState } from 'react';
import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreHorizontalIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import { Category } from '@/types/categories';
import { NotDataFoundHero } from '@/assets/tenant/owner/promotions';
import DeleteCategoryModal from './DeleteCategoryModal';
import UpdateCategoryModal from './UpdateCategoryModal';

interface CategoriesTableProps {
    categories: Category[];
}

interface UpdateModalState {
    isOpen: boolean;
    category: Category | null;
}

function CategoriesTable({ categories }: CategoriesTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const [udpateModalState, setUpdateModalState] = useState<UpdateModalState>({
        isOpen: false,
        category: null,
    });

    if (!categories.length) return <NoDataFound />;

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Category Name</TableHead>
                        <TableHead>Icon</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='text-center'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {categories.map((category) => (
                        <TableRow key={category.id}>
                            <TableHead className='w-[100px]'>
                                {category.id}
                            </TableHead>
                            <TableHead>{category.name}</TableHead>
                            <TableHead>NA (static)</TableHead>
                            <TableHead>NA (static)</TableHead>
                            <TableHead className='text-center'>
                                <DropdownMenu>
                                    <DropdownMenuTrigger>
                                        <MoreHorizontalIcon />
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                setUpdateModalState({
                                                    isOpen: true,
                                                    category,
                                                })
                                            }
                                            className='text-metalic-blue focus:text-blue-500'
                                        >
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            onClick={() =>
                                                setDeleteModalState({
                                                    isOpen: true,
                                                    id: category.id,
                                                })
                                            }
                                            className='text-red-500 focus:text-red-500'
                                        >
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableHead>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
            <DeleteCategoryModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
            />
            <UpdateCategoryModal
                isOpen={udpateModalState.isOpen}
                category={udpateModalState.category}
                onClose={() =>
                    setUpdateModalState({ isOpen: false, category: null })
                }
            />
        </>
    );
}

function NoDataFound() {
    return (
        <div className='grid grid-rows-[auto_1fr]'>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Category Name</TableHead>
                        <TableHead>Icon</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody></TableBody>
            </Table>
            <div className='grid place-items-center p-4'>
                <div className='space-y-8 text-center'>
                    <img
                        src={NotDataFoundHero}
                        alt='No Data Found Hero Image'
                        className='mx-auto'
                    />
                    <h1 className='text-[32px] font-semibold text-metalic-blue'>
                        No Category? No problem!
                    </h1>
                    <p className='text-xl'>
                        Click the{' '}
                        <span className='text-metalic-blue'>
                            “+ Create New Category”
                        </span>{' '}
                        or the{' '}
                        <span className='text-metalic-blue'>“Get Started”</span>{' '}
                        button below to get <br /> your business noticed.
                    </p>
                    <Button className='h-[40px] w-[136px] bg-metalic-blue font-medium hover:bg-metalic-blue/90'>
                        Get Started
                    </Button>
                </div>
            </div>
        </div>
    );
}

export default CategoriesTable;
