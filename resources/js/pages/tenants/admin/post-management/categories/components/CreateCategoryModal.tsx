import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';
import FormInput from '@/components/forms/FormInput';
import IconPicker from './IconPicker';

interface CreateCategoryModalProps {
    isOpen: boolean;
    onClose: () => void;
}

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

type CreateCategoryFields = z.infer<typeof validationSchema>;
const defaultValues: CreateCategoryFields = {
    name: '',
    icon: '',
};

function CreateCategoryModal({ isOpen, onClose }: CreateCategoryModalProps) {
    const form = useForm<CreateCategoryFields>({
        defaultValues,
        resolver: zodResolver(validationSchema),
    });

    const handleSubmit = form.handleSubmit((values) => console.log(values));

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='max-w-4xl'>
                <Form {...form}>
                    <form onSubmit={handleSubmit} className='space-y-4'>
                        <div>
                            <h1 className='text-[22px] font-medium'>
                                Create Category
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
                            >
                                Cancel
                            </Button>
                            <Button className='bg-metalic-blue text-[15px] font-medium hover:bg-metalic-blue/90'>
                                Create
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default CreateCategoryModal;
