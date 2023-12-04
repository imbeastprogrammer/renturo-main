import { z } from 'zod';
import _ from 'lodash';
import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';

import { User } from '@/types/users';
import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/forms/FormInput';
import useOwnerToast from '@/hooks/useOwnerToast';

const updateAdminSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().nonempty(),
    mobile_number: z.string().nonempty(),
    role: z.string().nonempty(),
});

type UpdateAdminFormFields = z.infer<typeof updateAdminSchema>;
const defaultValues: UpdateAdminFormFields = {
    first_name: '',
    last_name: '',
    email: '',
    mobile_number: '',
    role: 'ADMIN',
};

type UpdateAdminFormProps = {
    admin: User;
};
function UpdateAdminForm({ admin }: UpdateAdminFormProps) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useOwnerToast();

    const form = useForm<UpdateAdminFormFields>({
        defaultValues,
        resolver: zodResolver(updateAdminSchema),
        values: {
            first_name: admin.first_name,
            last_name: admin.last_name,
            email: admin.email,
            mobile_number: admin.mobile_number,
            role: admin.role,
        },
    });

    const onSubmit = form.handleSubmit((values) => {
        router.put(`/admin/users/${admin.id}`, values, {
            onBefore: () => setIsSubmitting(true),
            onSuccess: () => {
                toast.success({ description: 'The admin has been updated.' });
                router.visit('/admin/user-management/admins');
            },
            onError: (errors) =>
                toast.error({ description: _.valuesIn(errors)[0] }),
            onFinish: () => setIsSubmitting(false),
        });
    });
    const hasErrors = Object.keys(form.formState.errors).length > 0;

    return (
        <Form {...form}>
            <form onSubmit={onSubmit} className='grid gap-6 p-6'>
                <div>
                    <h1 className='text-[24px] font-semibold leading-none'>
                        General
                    </h1>
                    <p className='text-base text-[#2E3436]/50'>
                        Update a admin user.
                    </p>
                </div>
                <div className='flex items-start gap-4'>
                    <FormInput
                        label='First Name'
                        placeholder='First Name'
                        control={form.control}
                        name='first_name'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        label='Last Name'
                        placeholder='Last Name'
                        control={form.control}
                        name='last_name'
                        disabled={isSubmitting}
                    />
                </div>
                <div className='flex items-start gap-4'>
                    <FormInput
                        label='Email'
                        placeholder='Email Address'
                        control={form.control}
                        name='email'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        label='Mobile Number'
                        placeholder='Mobile Number'
                        control={form.control}
                        name='mobile_number'
                        disabled={isSubmitting}
                    />
                </div>
                {/* <div>
                    <h1 className='text-[24px] font-semibold leading-none'>
                        Role
                    </h1>
                    <p className='text-base text-[#2E3436]/50'>
                        Specify the user's permissions and access level.
                    </p>
                </div>
                <div className='grid grid-cols-2 gap-4'>
                    <FormInput
                        label='Role'
                        placeholder='-Role-'
                        control={form.control}
                        name='role'
                    />
                </div> */}
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
                <div className='flex justify-end gap-4'>
                    <Link href='/admin/user-management/admins'>
                        <Button
                            type='button'
                            variant='outline'
                            className='text-base'
                        >
                            Cancel
                        </Button>
                    </Link>
                    <Button
                        type='submit'
                        className='bg-metalic-blue text-base hover:bg-metalic-blue/90'
                        disabled={isSubmitting}
                    >
                        Update Admin
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default UpdateAdminForm;
