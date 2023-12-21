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

import { DynamicForm } from '@/types/dynamic-form';
import { NotDataFoundHero } from '@/assets/tenant/owner/promotions';
import DeleteCategoryModal from './DeleteDynamicFormModal';
import UpdateDynamicFormModal from './UpdateCategoryModal';

interface DynamicFormsTableProps {
    dynamicForms: DynamicForm[];
}

interface UpdateModalState {
    isOpen: boolean;
    dynamicForm: DynamicForm | null;
}

function DynamicFormsTable({ dynamicForms }: DynamicFormsTableProps) {
    const [deleteModalState, setDeleteModalState] = useState({
        isOpen: false,
        id: 0,
    });

    const [udpateModalState, setUpdateModalState] = useState<UpdateModalState>({
        isOpen: false,
        dynamicForm: null,
    });

    if (!dynamicForms.length) return <NoDataFound />;

    return (
        <>
            <Table className='overflow-auto'>
                <TableHeader className='sticky top-0 bg-white'>
                    <TableRow>
                        <TableHead className='w-[100px]'>ID</TableHead>
                        <TableHead>Form Name</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Sub-Category</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='text-center'>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {dynamicForms.map((dynamicForm) => (
                        <TableRow key={dynamicForm.id}>
                            <TableHead className='w-[100px]'>
                                {dynamicForm.id}
                            </TableHead>
                            <TableHead>{dynamicForm.name}</TableHead>
                            <TableHead>{dynamicForm.description}</TableHead>
                            <TableHead>
                                {dynamicForm.subcategory.category.name}
                            </TableHead>
                            <TableHead>
                                {dynamicForm.subcategory.name}
                            </TableHead>
                            <TableHead>Static</TableHead>
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
                                                    dynamicForm,
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
                                                    id: dynamicForm.id,
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
            <UpdateDynamicFormModal
                isOpen={udpateModalState.isOpen}
                dynamicForm={udpateModalState.dynamicForm}
                onClose={() =>
                    setUpdateModalState({ isOpen: false, dynamicForm: null })
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
                        <TableHead>Form Name</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Sub-Category</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className='text-center'>Action</TableHead>
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

export default DynamicFormsTable;
