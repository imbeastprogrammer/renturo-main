import _ from 'lodash';
import * as z from 'zod';
import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';

import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/forms/FormInput';
import FormSelect from '@/components/forms/FormSelect';
import useOwnerToast from '@/hooks/useOwnerToast';

const formSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    phone: z.string().min(11).max(11),
    email: z.string().email(),
    role: z.string().nonempty(),
});

function CreateUserForm() {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useOwnerToast();

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: '',
            last_name: '',
            phone: '',
            email: '',
            role: 'USER',
        },
    });

    const onSubmit = form.handleSubmit((values: z.infer<typeof formSchema>) => {
        router.post(
            '/admin/users',
            { ...values, mobile_number: values.phone },
            {
                onBefore: () => setIsSubmitting(true),
                onSuccess: (e) => {
                    toast.success({
                        title: 'Success',
                        description:
                            'The new user has been added to the system.',
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
                onFinish: () => setIsSubmitting(false),
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
                        Create a new user.
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
                        name='phone'
                        disabled={isSubmitting}
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
                        disabled={isSubmitting}
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
                        disabled={isSubmitting}
                        className='bg-metalic-blue text-base hover:bg-metalic-blue/90'
                    >
                        Add New User
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default CreateUserForm;
