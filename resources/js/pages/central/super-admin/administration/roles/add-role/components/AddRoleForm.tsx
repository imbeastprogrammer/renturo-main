import { z } from 'zod';
import { ReactNode } from 'react';
import { useToast } from '@/components/ui/use-toast';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';

import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin/forms/FormInput';
import FormSelect from '@/components/super-admin/forms/FormSelect';
import FormTextArea from '@/components/super-admin/forms/FormTextArea';

const addFormSchema = z.object({
    name: z.string().nonempty(),
    description: z.string().nonempty(),
    permissions: z.string().email().nonempty(),
});

type AddRoleFormFields = z.infer<typeof addFormSchema>;
const defaultValues: AddRoleFormFields = {
    name: '',
    description: '',
    permissions: '',
};

function AddRoleForm() {
    const { toast } = useToast();
    const form = useForm<AddRoleFormFields>({
        defaultValues,
        resolver: zodResolver(addFormSchema),
    });

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const onSubmit = form.handleSubmit(() => {});

    return (
        <Form {...form}>
            <form className='space-y-4' onSubmit={onSubmit}>
                <h1 className='text-base text-[#2E3436]/50'>
                    Create a new role for this site and make it available to
                    users.
                </h1>
                <div className='space-y-4'>
                    <SectionTitle>General</SectionTitle>
                    <div className='max-w-[760px] space-y-4'>
                        <FormInput
                            name='name'
                            label='Name'
                            placeholder='Name'
                            control={form.control}
                        />
                        <FormTextArea
                            name='description'
                            label='Description'
                            placeholder='Description'
                            control={form.control}
                        />
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Access Control</SectionTitle>
                    <div>
                        <FormSelect
                            name='permissions'
                            label='Permissions'
                            control={form.control}
                            data={[
                                {
                                    label: 'Administrator',
                                    value: 'administrator',
                                },
                            ]}
                        />
                    </div>
                </div>
                <div className='flex justify-end text-base'>
                    {hasErrors && (
                        <div className='flex items-center gap-2'>
                            <img src={ErrorIcon} />
                            <p>
                                Please complete all required fields before
                                submitting.
                            </p>
                        </div>
                    )}
                </div>
                <div className='flex justify-end'>
                    <Button
                        type='submit'
                        className='bg-[#84C58A] px-8 text-base font-medium hover:bg-[#84C58A]/90'
                    >
                        Add New Role
                    </Button>
                </div>
            </form>
        </Form>
    );
}

type SectionTitleProps = { children: ReactNode };

function SectionTitle({ children }: SectionTitleProps) {
    return (
        <div className='rounded-lg bg-[#F0F0F0] px-4 py-2 text-[18px] font-semibold'>
            <h2>{children}</h2>
        </div>
    );
}

export default AddRoleForm;
