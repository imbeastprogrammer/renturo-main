import _ from 'lodash';
import { z } from 'zod';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { Form } from '@/components/ui/form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin/forms/FormInput';
import useCentralToast from '@/hooks/useCentralToast';

const changePasswordSchema = z
    .object({
        old_password: z.string().min(8).max(16),
        confirm_old_password: z.string(),
        new_password: z.string().min(8).max(16),
        confirm_new_password: z.string(),
    })
    .superRefine((fields, ctx) => {
        if (fields.old_password !== fields.confirm_old_password) {
            ctx.addIssue({
                code: 'custom',
                message: 'Old Password and Confirm Old Password does not match',
                path: ['confirm_old_password'],
            });
        }
        if (fields.new_password !== fields.confirm_new_password) {
            ctx.addIssue({
                code: 'custom',
                message: 'New Password and Confirm New Password does not match',
                path: ['confirm_new_password'],
            });
        }
    });

type ChangePasswordFormFields = z.infer<typeof changePasswordSchema>;
const defaultValues: ChangePasswordFormFields = {
    old_password: '',
    confirm_old_password: '',
    new_password: '',
    confirm_new_password: '',
};

function ChangePasswordForm() {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useCentralToast();

    const form = useForm<ChangePasswordFormFields>({
        defaultValues,
        resolver: zodResolver(changePasswordSchema),
    });

    const onSubmit = form.handleSubmit(
        ({ confirm_new_password, new_password, old_password }) => {
            router.post(
                '/super-admin/settings/update-password',
                {
                    old_password,
                    new_password,
                    confirm_password: confirm_new_password,
                },
                {
                    onBefore: () => setIsSubmitting(true),
                    onSuccess: () => {
                        toast.success({
                            description:
                                'The password has been changed successfully.',
                        });
                        router.post('/logout');
                    },
                    onError: (error) =>
                        toast.error({ description: _.valuesIn(error)[0] }),
                    onFinish: () => setIsSubmitting(false),
                },
            );
        },
    );
    const hasErrors = Object.keys(form.formState.errors).length > 0;

    return (
        <Form {...form}>
            <form className='space-y-6 p-4' onSubmit={onSubmit}>
                <div>
                    <h1 className='text-xl font-medium'>Password</h1>
                    <p className='text-black/50'>
                        Update your password now to protect your account from
                        unauthorized access.
                    </p>
                </div>
                <div className='grid grid-cols-2 gap-4'>
                    <FormInput
                        type='password'
                        control={form.control}
                        name='old_password'
                        label='Old Password'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        type='password'
                        control={form.control}
                        name='confirm_old_password'
                        label='Confirm Old Password'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        type='password'
                        control={form.control}
                        name='new_password'
                        label='New Password'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                    <FormInput
                        type='password'
                        control={form.control}
                        name='confirm_new_password'
                        label='Confirm New Password'
                        orientation='vertical'
                        disabled={isSubmitting}
                    />
                </div>
                <p className='text-black/50'>
                    It should be at least 6 characters long and include a mix of
                    upper and lowercase letters, numbers, and symbols.
                </p>
                <div>
                    {hasErrors &&
                        _.valuesIn(form.formState.errors).map((error, i) => (
                            <div key={i} className='flex items-center gap-2'>
                                <img src={ErrorIcon} />
                                <p>{error.message}</p>
                            </div>
                        ))}
                </div>
                <div className='flex justify-end gap-2'>
                    <Button variant='outline' className='font-medium'>
                        Cancel
                    </Button>
                    <Button
                        type='submit'
                        className='bg-[#84C58A] font-medium hover:bg-[#84C58A]/90'
                        disabled={isSubmitting}
                    >
                        Save Changes
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default ChangePasswordForm;
