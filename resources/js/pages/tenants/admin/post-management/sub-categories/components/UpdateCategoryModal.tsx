import _ from 'lodash';
import { z } from 'zod';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';

import { FormattedSubCategory } from '@/types/categories';
import FormInput from '@/components/forms/FormInput';
import IconPicker from './IconPicker';
import useOwnerToast from '@/hooks/useOwnerToast';

const validationSchema = z.object({
    name: z.string().nonempty('Name is required'),
    icon: z
        .string()
        .optional()
        .or(
            z
                .custom<File>()
                .refine(
                    (file) =>
                        !file || (!!file && file.size <= 10 * 1024 * 1024),
                    {
                        message:
                            'The profile picture must be a maximum of 10MB.',
                    },
                )
                .refine(
                    (file) =>
                        !file || (!!file && file.type?.startsWith('image')),
                    {
                        message: 'Only images are allowed to be sent.',
                    },
                ),
        ),
});

type UpdateSubCategoryFields = z.infer<typeof validationSchema>;
const defaultValues: UpdateSubCategoryFields = {
    name: '',
    icon: '',
};

type UpdateModalProps = {
    isOpen: boolean;
    onClose: () => void;
    subCategory: FormattedSubCategory | null;
};

function UpdateSubCategoryModal({
    isOpen,
    onClose,
    subCategory,
}: UpdateModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const toast = useOwnerToast();

    const form = useForm<UpdateSubCategoryFields>({
        defaultValues,
        values: { name: subCategory?.sub_category_name || '' },
        resolver: zodResolver(validationSchema),
    });

    const handleSubmit = form.handleSubmit((values) =>
        router.put(
            `/admin/categories/${subCategory?.sub_category_id}`,
            values,
            {
                onBefore: () => setIsLoading(true),
                onFinish: () => setIsLoading(false),
                onSuccess: () => {
                    toast.success({
                        description: 'New Category has been added.',
                    });
                    onClose();
                },
                onError: (err) =>
                    toast.error({ description: _.valuesIn(err)[0] }),
            },
        ),
    );

    const handleClose = () => !isLoading && onClose();

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className='max-w-4xl'>
                <Form {...form}>
                    <form onSubmit={handleSubmit} className='space-y-4'>
                        <div>
                            <h1 className='text-[22px] font-medium'>
                                Update Category
                            </h1>
                        </div>
                        <div className='space-y-4'>
                            <FormInput
                                name='name'
                                label='Category Name'
                                control={form.control}
                                className='h-[45px]'
                            />
                            <IconPicker
                                label='Icon'
                                name='icon'
                                control={form.control}
                                description='Upload tips: size under [file limit]kb, resolution around [recommended]px, file format PNG or SVG.'
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

export default UpdateSubCategoryModal;
