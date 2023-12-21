import { z } from 'zod';
import _ from 'lodash';
import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';
import { FormSelect, FormInput, FormTextAreaInput } from '@/components/forms';

import { Category, SubCategory } from '@/types/categories';
import { DynamicForm } from '@/types/dynamic-form';
import useOwnerToast from '@/hooks/useOwnerToast';

const validationSchema = z.object({
    name: z.string().nonempty('Name is required.'),
    description: z.string().nonempty('Description is required.'),
    category_id: z.string().nonempty('Category is required.'),
    subcategory_id: z.string().nonempty('Sub-Category is required.'),
});

type UpdateDynamicFormFields = z.infer<typeof validationSchema>;
const defaultValues: UpdateDynamicFormFields = {
    name: '',
    description: '',
    category_id: '',
    subcategory_id: '',
};

interface UpdateDynamicFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    subCategories: SubCategory[];
    dynamicForm: DynamicForm | null;
    categories: Category[];
}

function UpdateDynamicFormModal({
    isOpen,
    onClose,
    dynamicForm,
}: UpdateDynamicFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const toast = useOwnerToast();
    const props = usePage().props;

    const categories = props.categories as Category[];
    const subCategories = props.subCategories as SubCategory[];

    const form = useForm<UpdateDynamicFormFields>({
        defaultValues,
        values: {
            name: dynamicForm?.name || '',
            description: dynamicForm?.description || '',
            subcategory_id: dynamicForm?.subcategory.id.toString() || '',
            category_id: dynamicForm?.subcategory.category.id.toString() || '',
        },
        resolver: zodResolver(validationSchema),
    });

    const selectedCategoryId = form.watch('category_id');

    const subCategoriesOptions = subCategories
        .filter(({ category_id }) => category_id === Number(selectedCategoryId))
        .map(({ id, name }) => ({
            label: name,
            value: id.toString(),
        }));

    const categoriesOptions = categories.map(({ id, name }) => ({
        label: name,
        value: id.toString(),
    }));

    const handleSubmit = form.handleSubmit((values) =>
        router.put(`/admin/form/${dynamicForm?.id}`, values, {
            onBefore: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            onSuccess: () => {
                toast.success({ description: 'Form has been updated.' });
                onClose();
            },
            onError: (err) => toast.error({ description: _.valuesIn(err)[0] }),
        }),
    );

    const handleClose = () => !isLoading && onClose();

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className='max-w-4xl'>
                <Form {...form}>
                    <form onSubmit={handleSubmit} className='space-y-4'>
                        <div>
                            <h1 className='text-[22px] font-medium'>
                                Update Your Form
                            </h1>
                        </div>
                        <div className='space-y-4'>
                            <FormInput
                                name='name'
                                label='Form Name'
                                control={form.control}
                                className='h-[45px]'
                            />
                            <div className='grid grid-cols-2 gap-4'>
                                <FormSelect
                                    control={form.control}
                                    name='category_id'
                                    label='Category'
                                    data={categoriesOptions}
                                    className='h-[45px]'
                                />
                                <FormSelect
                                    control={form.control}
                                    name='subcategory_id'
                                    label='Sub Category'
                                    data={subCategoriesOptions}
                                    className='h-[45px]'
                                />
                            </div>
                            <FormTextAreaInput
                                label='Description'
                                name='description'
                                control={form.control}
                                rows={5}
                            />
                        </div>
                        <div className='flex justify-end gap-4'>
                            <Button
                                variant='outline'
                                className='text-[15px] font-medium'
                                type='button'
                                onClick={handleClose}
                            >
                                Cancel
                            </Button>
                            <Button
                                disabled={isLoading}
                                className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'
                            >
                                Update
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default UpdateDynamicFormModal;
