import _ from 'lodash';
import * as z from 'zod';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';

import { User } from '@/types/users';
import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/forms/FormInput';
import FormSelect from '@/components/forms/FormSelect';
import useOwnerToast from '@/hooks/useOwnerToast';

const formSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    phone: z.string().optional(),
    email: z.union([z.literal(''), z.string().email()]),
    role: z.string().nonempty(),
});

type UpdateUserFormFields = z.infer<typeof formSchema>;

const defaultValues: UpdateUserFormFields = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    role: '',
};

type UpdateUserFormProps = {
    user: User;
};

function UpdateUserForm({ user }: UpdateUserFormProps) {
    const toast = useOwnerToast();
    const form = useForm<z.infer<typeof formSchema>>({
        defaultValues,
        resolver: zodResolver(formSchema),
        values: {
            first_name: user.first_name,
            last_name: user.last_name,
            phone: user.verified_mobile_no.mobile_no,
            email: user.email,
            role: user.role,
        },
    });

    const onSubmit = form.handleSubmit(({ first_name, last_name, role }) => {
        router.put(
            `/admin/users/${user.id}`,
            { first_name, last_name, role },
            {
                onSuccess: () => {
                    toast.success({
                        title: 'Success',
                        description: 'The user has been updated to the system.',
                    });
                    router.visit('/admin/user-management/users', {
                        replace: true,
                    });
                },
                onError: (error) => {
                    toast.error({
                        title: 'Error',
                        description:
                            _.valuesIn(error)[0] ||
                            'Something went wrong, Please try again later.',
                    });
                },
            },
        );
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
                        Update a user.
                    </p>
                </div>
                <div className='flex items-start gap-4'>
                    <FormInput
                        label='First Name'
                        placeholder='First Name'
                        control={form.control}
                        name='first_name'
                    />
                    <FormInput
                        label='Last Name'
                        placeholder='Last Name'
                        control={form.control}
                        name='last_name'
                    />
                </div>
                <div className='flex items-start gap-4'>
                    <FormInput
                        label='Email'
                        placeholder='Email Address'
                        control={form.control}
                        name='email'
                        disabled
                    />
                    <FormInput
                        label='Mobile Number'
                        placeholder='Mobile Number'
                        control={form.control}
                        name='phone'
                        disabled
                    />
                </div>
                <div>
                    <h1 className='text-[24px] font-semibold leading-none'>
                        Role
                    </h1>
                    <p className='text-base text-[#2E3436]/50'>
                        Specify the user's permissions and access level.
                    </p>
                </div>
                <div className='grid grid-cols-2 gap-4'>
                    <FormSelect
                        name='role'
                        label='Role'
                        placeholder='-Role-'
                        data={[
                            { label: 'ADMIN', value: 'ADMIN' },
                            { label: 'OWNER', value: 'OWNER' },
                            { label: 'USER', value: 'USER' },
                        ]}
                        control={form.control}
                    />
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
                <div className='flex justify-end gap-4'>
                    <Link href='/admin/user-management/users'>
                        <Button variant='outline' className='text-base'>
                            Cancel
                        </Button>
                    </Link>
                    <Button
                        type='submit'
                        className='bg-metalic-blue text-base hover:bg-metalic-blue/90'
                    >
                        Update Details
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default UpdateUserForm;
