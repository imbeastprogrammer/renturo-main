import { z } from 'zod';
import _ from 'lodash';
import { ReactNode, useState } from 'react';
import { router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';

import { User } from '@/types/users';
import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin/forms/FormInput';
import FormSelect from '@/components/super-admin/forms/FormSelect';
import useCentralToast from '@/hooks/useCentralToast';
import getSuccessMessage from '@/lib/getSuccessMessage';

const editUserFormSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().email().nonempty(),
    mobile_number: z.string().optional(),
    role: z.string().optional(),
    status: z.string().optional(),
});

type EditUserFormFields = z.infer<typeof editUserFormSchema>;
const defaultValues: EditUserFormFields = {
    first_name: '',
    last_name: '',
    email: '',
    mobile_number: '',
    role: '',
    status: '',
};

type EditUserFormProps = { user: User };

function EditUserForm({ user }: EditUserFormProps) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useCentralToast();

    const form = useForm<EditUserFormFields>({
        defaultValues,
        values: { ...user },
        resolver: zodResolver(editUserFormSchema),
    });

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const onSubmit = form.handleSubmit((values) => {
        router.put(`/super-admin/users/${user.id}`, values, {
            onBefore: () => setIsSubmitting(true),
            onSuccess: (data) => {
                toast.success({
                    description: getSuccessMessage(data),
                });
                router.replace('/super-admin/administration/user-management');
            },
            onError: (error) =>
                toast.error({
                    description: _.valuesIn(error)[0],
                }),
            onFinish: () => setIsSubmitting(false),
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
                            disabled={isSubmitting}
                        />
                        <FormInput
                            name='last_name'
                            label='Last Name'
                            placeholder='Last Name'
                            control={form.control}
                            disabled={isSubmitting}
                        />
                        <FormInput
                            name='email'
                            label='Email Address'
                            placeholder='Email Address'
                            control={form.control}
                            disabled={isSubmitting}
                        />
                        <FormInput
                            name='mobile_number'
                            label='Mobile Number'
                            placeholder='Mobile Number'
                            control={form.control}
                            disabled={isSubmitting}
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
                            disabled={isSubmitting}
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
                            disabled={isSubmitting}
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
                        disabled={isSubmitting}
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
