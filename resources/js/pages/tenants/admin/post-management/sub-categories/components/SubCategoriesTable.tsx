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

import { FormattedSubCategory } from '@/types/categories';
import { NotDataFoundHero } from '@/assets/tenant/owner/promotions';
import DeleteSubCategoryModal from './DeleteSubCategoryModal';
import UpdateSubCategoryModal from './UpdateCategoryModal';

interface SubCategoriesTableProps {
    subCategories: FormattedSubCategory[];
}

interface UpdateModalState {
    isOpen: boolean;
    subCategory: FormattedSubCategory | null;
}

function SubCategoriesTable({ subCategories }: SubCategoriesTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const [udpateModalState, setUpdateModalState] = useState<UpdateModalState>({
        isOpen: false,
        subCategory: null,
    });

    if (!subCategories.length) return <NoDataFound />;

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Sub-Category Name</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='text-center'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {subCategories.map((subCategory) => (
                        <TableRow key={subCategory.sub_category_id}>
                            <TableHead className='w-[100px]'>
                                {subCategory.sub_category_id}
                            </TableHead>
                            <TableHead>
                                {subCategory.sub_category_name}
                            </TableHead>
                            <TableHead>{subCategory.category_name}</TableHead>
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
                                                    subCategory: subCategory,
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
                                                    id: subCategory.sub_category_id,
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
            <DeleteSubCategoryModal
                isOpen={deleteModalState.isOpen}
                id={deleteModalState.id}
                onClose={() => setDeleteModalState({ isOpen: false, id: 0 })}
            />
            <UpdateSubCategoryModal
                isOpen={udpateModalState.isOpen}
                subCategory={udpateModalState.subCategory}
                onClose={() =>
                    setUpdateModalState({ isOpen: false, subCategory: null })
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

export default SubCategoriesTable;
