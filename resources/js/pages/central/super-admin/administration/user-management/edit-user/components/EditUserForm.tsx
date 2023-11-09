import { z } from 'zod';
import _ from 'lodash';
import { ReactNode } from 'react';
import { useToast } from '@/components/ui/use-toast';
import { router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';

import { User } from '@/types/users';
import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin/forms/FormInput';
import FormSelect from '@/components/super-admin/forms/FormSelect';

const editUserFormSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().email().nonempty(),
    mobile_no: z.string().optional(),
    role: z.string().optional(),
    status: z.string().optional(),
});

type EditUserFormFields = z.infer<typeof editUserFormSchema>;
const defaultValues: EditUserFormFields = {
    first_name: '',
    last_name: '',
    email: '',
    mobile_no: '',
    role: '',
    status: '',
};

type EditUserFormProps = { user: User };

function EditUserForm({ user }: EditUserFormProps) {
    const { toast } = useToast();
    const form = useForm<EditUserFormFields>({
        defaultValues,
        values: { ...user },
        resolver: zodResolver(editUserFormSchema),
    });

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const onSubmit = form.handleSubmit((values) => {
        router.put(`/super-admin/users/${user.id}`, values, {
            onSuccess: () => {
                toast({
                    title: 'Success',
                    description: 'The user has been updated successfully.',
                    style: {
                        marginBottom: '1rem',
                        transform: 'translateX(-1rem)',
                    },
                    variant: 'default',
                });
                router.visit('/super-admin/administration/user-management', {
                    replace: true,
                });
            },
            onError: (error) =>
                toast({
                    title: 'Error',
                    description:
                        _.valuesIn(error)[0] ||
                        'Something went wrong, Please try again later.',
                    style: {
                        marginBottom: '1rem',
                        transform: 'translateX(-1rem)',
                    },
                    variant: 'destructive',
                }),
        });
    });

    return (
        <Form {...form}>
            <form className='space-y-4' onSubmit={onSubmit}>
                <h1 className='text-base text-[#2E3436]/50'>
                    Update user and assign them to this site.
                </h1>
                <div className='space-y-4'>
                    <SectionTitle>General</SectionTitle>
                    <div className='max-w-[760px] space-y-4'>
                        <FormInput
                            name='first_name'
                            label='First Name'
                            placeholder='First Name'
                            control={form.control}
                        />
                        <FormInput
                            name='last_name'
                            label='Last Name'
                            placeholder='Last Name'
                            control={form.control}
                        />
                        <FormInput
                            name='email'
                            label='Email Address'
                            placeholder='Email Address'
                            control={form.control}
                        />
                        <FormInput
                            name='mobile_no'
                            label='Mobile Number'
                            placeholder='Mobile Number'
                            control={form.control}
                        />
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Role</SectionTitle>
                    <div>
                        <FormSelect
                            name='role'
                            label='Role'
                            control={form.control}
                            data={[
                                {
                                    label: 'Administrator',
                                    value: 'administrator',
                                },
                                {
                                    label: 'Super Admin',
                                    value: 'SUPER-ADMIN',
                                },
                            ]}
                        />
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Status</SectionTitle>
                    <div>
                        <FormSelect
                            name='status'
                            label='Status'
                            control={form.control}
                            data={[
                                {
                                    label: 'Active',
                                    value: 'active',
                                },
                                {
                                    label: 'Inactive',
                                    value: 'inactive',
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
                        Update Details
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
            <h1>{children}</h1>
        </div>
    );
}

export default EditUserForm;
