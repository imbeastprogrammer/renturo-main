import { z } from 'zod';
import _ from 'lodash';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';

import useOwnerToast from '@/hooks/useOwnerToast';
import { FormSelect, FormInput, FormTextAreaInput } from '@/components/forms';
import { SubCategory } from '@/types/categories';

const validationSchema = z.object({
    name: z.string().nonempty('Name is required'),
    description: z.string().nonempty('Description is required.'),
    category_id: z.string().optional(),
    subcategory_id: z.string().nonempty('Sub-Category is Required'),
});

type CreateDynamicFormFields = z.infer<typeof validationSchema>;
const defaultValues: CreateDynamicFormFields = {
    name: '',
    description: '',
    category_id: '',
    subcategory_id: '',
};

interface CreateDynamicFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    subCategories: SubCategory[];
}

function CreateDynamicFormModal({
    isOpen,
    onClose,
    subCategories,
}: CreateDynamicFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const toast = useOwnerToast();

    const subCategoriesOptions = subCategories.map(({ id, name }) => ({
        label: name,
        value: id.toString(),
    }));

    const form = useForm<CreateDynamicFormFields>({
        defaultValues,
        resolver: zodResolver(validationSchema),
    });

    const handleSubmit = form.handleSubmit((values) =>
        router.post('/admin/form', values, {
            onBefore: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            onSuccess: () => {
                toast.success({ description: 'New Form has been added.' });
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
                                Build Your Form
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
                                    data={[]}
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
                                Create
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default CreateDynamicFormModal;
