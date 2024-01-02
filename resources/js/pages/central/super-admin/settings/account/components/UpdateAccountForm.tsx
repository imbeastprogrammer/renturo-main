import _ from 'lodash';
import { z } from 'zod';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form, FormField } from '@/components/ui/form';
import { Button } from '@/components/ui/button';

import { ErrorIcon } from '@/assets/central';
import { User } from '@/types/users';
import FormInput from '@/components/super-admin/forms/FormInput';
import ProfilePicturePicker from './ProfilePicturePicker';
import useCentralToast from '@/hooks/useCentralToast';
import getSuccessMessage from '@/lib/getSuccessMessage';

const updateAccountSchema = z.object({
    profile_picture: z
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
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().nonempty(),
    contact_no: z.string().nonempty().length(11),
    address: z.string().optional(),
    city: z.string().optional(),
    province: z.string().optional(),
    country: z.string().optional(),
    zipcode: z.string().optional(),
});

type UpdateAccountFormFields = z.infer<typeof updateAccountSchema>;

const defaultValues: UpdateAccountFormFields = {
    profile_picture: '',
    first_name: '',
    last_name: '',
    email: '',
    contact_no: '',
    address: '',
    city: '',
    province: '',
    country: '',
    zipcode: '',
};

type UpdateAccountForm = { user: User };

function UpdateAccountForm({ user }: UpdateAccountForm) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useCentralToast();

    const form = useForm<UpdateAccountFormFields>({
        defaultValues,
        resolver: zodResolver(updateAccountSchema),
        values: {
            ...defaultValues,
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            contact_no: user.mobile_number,
        },
    });

    const onSubmit = form.handleSubmit(({ contact_no, ...values }) => {
        router.post(
            '/super-admin/settings/update-user-profile',
            { mobile_number: contact_no, ...values },
            {
                onBefore: () => setIsSubmitting(true),
                onSuccess: (data) =>
                    toast.success({
                        description: getSuccessMessage(data),
                    }),
                onError: (errors) =>
                    toast.error({ description: _.valuesIn(errors)[0] }),
                onFinish: () => setIsSubmitting(false),
            },
        );
    });

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    return (
        <Form {...form}>
            <form onSubmit={onSubmit} className='space-y-6 p-4'>
                <div>
                    <h1 className='text-xl font-medium'>General Info</h1>
                    <p className='text-black/50'>
                        Update your photo and personal details here.
                    </p>
                </div>
                <FormField
                    control={form.control}
                    name='profile_picture'
                    render={({ field }) => (
                        <ProfilePicturePicker
                            value={field.value || ''}
                            onChange={field.onChange}
                            disabled={isSubmitting}
                        />
                    )}
                />
                <div className='grid grid-cols-2 gap-4'>
                    <FormInput
                        control={form.control}
                        name='first_name'
                        label='First Name'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='last_name'
                        label='Last Name'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='email'
                        label='Email Address'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='contact_no'
                        label='Contact Number'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <div className='col-span-2'>
                        <FormInput
                            control={form.control}
                            name='address'
                            label='Address'
                            orientation='vertical'
                            disabled={isSubmitting}
                        />
                    </div>
                    <FormInput
                        control={form.control}
                        name='city'
                        label='City'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='province'
                        label='Province'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='country'
                        label='Country'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        control={form.control}
                        name='zipcode'
                        label='Zipcode'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                </div>
                <div>
                    {hasErrors &&
                        _.valuesIn(form.formState.errors).map((error, i) => (
                            <div key={i} className='flex items-center gap-2'>
                                <img src={ErrorIcon} />
                                <p>{error.message}</p>
                            </div>
                        ))}
                </div>
                <div className='flex justify-end gap-4'>
                    <Button variant='outline'>Cancel</Button>
                    <Button
                        type='submit'
                        className='bg-[#84C58A] hover:bg-[#84C58A]/90'
                        disabled={isSubmitting}
                    >
                        Save Changes
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default UpdateAccountForm;
