import { z } from 'zod';
import _ from 'lodash';
import { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';

import { Category } from '@/types/categories';
import FormInput from '@/components/forms/FormInput';
import useOwnerToast from '@/hooks/useOwnerToast';
import FormSelect from '@/components/forms/FormSelect';
import getSuccessMessage from '@/lib/getSuccessMessage';

const validationSchema = z.object({
    name: z.string().nonempty('Name is required'),
    category_id: z.string().nonempty('Category is required'),
});

type CreateSubCategoryFields = z.infer<typeof validationSchema>;
const defaultValues: CreateSubCategoryFields = {
    name: '',
    category_id: '',
};
interface CreateModalProps {
    isOpen: boolean;
    onClose: () => void;
}

function CreateSubCategoryModal({ isOpen, onClose }: CreateModalProps) {
    const props = usePage().props;
    const categories = props.categories as Category[];

    const [isLoading, setIsLoading] = useState(false);
    const toast = useOwnerToast();

    const categoriesOptions = categories.map(({ name, id }) => ({
        label: name,
        value: id.toString(),
    }));

    const form = useForm<CreateSubCategoryFields>({
        defaultValues,
        resolver: zodResolver(validationSchema),
    });

    const handleSubmit = form.handleSubmit((values) =>
        router.post('/admin/sub-categories', values, {
            onBefore: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            onSuccess: (data) => {
                toast.success({
                    description: getSuccessMessage(data),
                });
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
                                Create Sub-Category
                            </h1>
                        </div>
                        <div className='space-y-4'>
                            <FormInput
                                name='name'
                                label='Sub-Category Name'
                                control={form.control}
                                className='h-[45px]'
                            />
                            <FormSelect
                                label='Category'
                                name='category_id'
                                control={form.control}
                                data={categoriesOptions}
                                className='h-[45px]'
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

export default CreateSubCategoryModal;
