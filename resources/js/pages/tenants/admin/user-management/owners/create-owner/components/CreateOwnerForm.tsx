import _ from 'lodash';
import { z } from 'zod';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';

import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/forms/FormInput';
import useOwnerToast from '@/hooks/useOwnerToast';

const createOwnerSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().nonempty(),
    mobile_no: z.string().nonempty(),
    role: z.string().nonempty(),
});

type CreateOwnerFormFields = z.infer<typeof createOwnerSchema>;
const defaultValues: CreateOwnerFormFields = {
    first_name: '',
    last_name: '',
    email: '',
    mobile_no: '',
    role: 'OWNER',
};

function CreateOwnerForm() {
    const toast = useOwnerToast();

    const form = useForm<CreateOwnerFormFields>({
        defaultValues,
        resolver: zodResolver(createOwnerSchema),
    });

    const onSubmit = form.handleSubmit((values) => {
        router.post('/admin/users', values, {
            onSuccess: () => {
                toast.success({ description: 'New user has been added.' });
                router.visit('/admin/user-management/owners');
            },
            onError: (errors) =>
                toast.error({ description: _.valuesIn(errors)[0] }),
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
                        Create a new Owner user.
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
                    />
                    <FormInput
                        label='Mobile Number'
                        placeholder='Mobile Number'
                        control={form.control}
                        name='mobile_no'
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
                    <Link href='/admin/user-management/owners?active=Users'>
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
                    >
                        Add New Owner
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default CreateOwnerForm;
