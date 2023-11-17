import _ from 'lodash';
import { z } from 'zod';
import { ReactNode } from 'react';
import { router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';

import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin/forms/FormInput';
import FormCheckbox from '@/components/super-admin/forms/FormCheckbox';
import FormSelect from '@/components/super-admin/forms/FormSelect';
import useCentralToast from '@/hooks/useCentralToast';

const addUserFormSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().email().nonempty(),
    mobile_number: z.string().nonempty(),
    allow_send_notification: z.boolean(),
    role: z.string().nonempty(),
});

type AddUserFormFields = z.infer<typeof addUserFormSchema>;
const defaultValues: AddUserFormFields = {
    first_name: '',
    last_name: '',
    email: '',
    mobile_number: '',
    allow_send_notification: false,
    role: '',
};

function AddUserForm() {
    const toast = useCentralToast();
    const form = useForm<AddUserFormFields>({
        defaultValues,
        resolver: zodResolver(addUserFormSchema),
    });

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const onSubmit = form.handleSubmit(
        ({ first_name, last_name, email, mobile_number }) => {
            router.post(
                '/super-admin/users',
                { first_name, last_name, email, mobile_number },
                {
                    onSuccess: () => {
                        toast.success({
                            title: 'Success',
                            description:
                                'The new user has been added to the system.',
                        });
                        router.visit(
                            '/super-admin/administration/user-management',
                            { replace: true },
                        );
                    },
                    onError: (error) =>
                        toast.error({
                            title: 'Error',
                            description:
                                _.valuesIn(error)[0] ||
                                'Something went wrong, Please try again later.',
                        }),
                },
            );
        },
    );

    return (
        <Form {...form}>
            <form className='space-y-4' onSubmit={onSubmit}>
                <h1 className='text-base text-[#2E3436]/50'>
                    Create a new user and assign them to this site.
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
                            name='mobile_number'
                            label='Mobile Number'
                            placeholder='Mobile Number'
                            control={form.control}
                        />
                        <FormCheckbox
                            name='allow_send_notification'
                            label='Send User Notification'
                            control={form.control}
                            description='Send the new user a welcome email with their account details.'
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
                        Add New Admin
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

export default AddUserForm;
