import _ from 'lodash';
import { z } from 'zod';
import { ReactNode, useState } from 'react';
import { router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { EyeIcon } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';
import { cn } from '@/lib/utils';

import { SuccessIcon, ErrorIcon } from '@/assets/tenant/form';
import FormInput from '@/components/forms/FormInput';
import useOwnerToast from '@/hooks/useOwnerToast';

const atleastSixCharacter = z
    .string()
    .min(6, 'Password must be at least 6 characters long');
const hasUppercase = z.string().refine((password) => /[A-Z]/.test(password), {
    message: 'Password must contain at least one uppercase letter',
});
const hasDigit = z.string().refine((password) => /\d/.test(password), {
    message: 'Password must contain at least one digit',
});
const hasSpecialChar = z
    .string()
    .refine((password) => /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/.test(password), {
        message: 'Password must contain at least one special character',
    });

const changePasswordSchema = z
    .object({
        current_password: z.string().nonempty(),
        new_password: z
            .string()
            .and(hasUppercase)
            .and(hasDigit)
            .and(hasSpecialChar)
            .and(atleastSixCharacter),
        confirm_password: z.string().nonempty(),
    })
    .superRefine((fields, ctx) => {
        if (fields.new_password !== fields.confirm_password) {
            ctx.addIssue({
                code: 'custom',
                message: 'The password does not match',
                path: ['confirm_password'],
            });
        }
    });

type ChangePasswordFormFields = z.infer<typeof changePasswordSchema>;

const defaultValues: ChangePasswordFormFields = {
    current_password: '',
    new_password: '',
    confirm_password: '',
};

function ChangePasswordForm() {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const toast = useOwnerToast();

    const form = useForm<ChangePasswordFormFields>({
        defaultValues,
        resolver: zodResolver(changePasswordSchema),
    });

    const onSubmit = form.handleSubmit((values) => {
        router.post(
            '/admin/settings/change-password',
            {
                old_password: values.current_password,
                new_password: values.new_password,
                confirm_password: values.confirm_password,
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
    });
    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const newPassword = form.watch('new_password');
    const passedSixCharValidation = atleastSixCharacter.safeParse(newPassword);
    const passedHasDigitValidation = hasDigit.safeParse(newPassword);
    const passedHasUppercaseValidaiton = hasUppercase.safeParse(newPassword);
    const passedHasSpecialCharValidation =
        hasSpecialChar.safeParse(newPassword);

    return (
        <Form {...form}>
            <form onSubmit={onSubmit} className='space-y-6 p-4'>
                <h1 className='text-2xl font-semibold'>Change Password</h1>
                <div className='grid grid-cols-2 gap-4'>
                    <FormInput
                        label='Current Password'
                        control={form.control}
                        name='current_password'
                        type='password'
                        icon={EyeIcon}
                        showError={false}
                        disabled={isSubmitting}
                    />
                    <div></div>
                    <FormInput
                        label='New Password'
                        control={form.control}
                        name='new_password'
                        type='password'
                        icon={EyeIcon}
                        showError={false}
                        disabled={isSubmitting}
                    />
                    <FormInput
                        label='Confirm Password'
                        control={form.control}
                        name='confirm_password'
                        type='password'
                        icon={EyeIcon}
                        showError={false}
                        disabled={isSubmitting}
                    />
                </div>
                <div>
                    {hasErrors && (
                        <ValidationMessage status='error'>
                            {_.valuesIn(form.formState.errors)[0].message}
                        </ValidationMessage>
                    )}
                    <ValidationMessage
                        status={
                            passedSixCharValidation.success
                                ? 'success'
                                : 'error'
                        }
                    >
                        Password must be at least 6 characters long
                    </ValidationMessage>
                    <ValidationMessage
                        status={
                            passedHasSpecialCharValidation.success
                                ? 'success'
                                : 'error'
                        }
                    >
                        Passwords must have at least one special character
                    </ValidationMessage>
                    <ValidationMessage
                        status={
                            passedHasDigitValidation.success
                                ? 'success'
                                : 'error'
                        }
                    >
                        Passwords must have at least one digit ('0'-'9')
                    </ValidationMessage>
                    <ValidationMessage
                        status={
                            passedHasUppercaseValidaiton.success
                                ? 'success'
                                : 'error'
                        }
                    >
                        Passwords must have at least one uppercase ('A'-'Z')
                    </ValidationMessage>
                </div>
                <div className='flex justify-end gap-4'>
                    <Button
                        variant='outline'
                        className='w-[140px] text-lg font-semibold'
                        type='button'
                    >
                        Cancel
                    </Button>
                    <Button
                        type='submit'
                        className='w-[140px] bg-metalic-blue text-lg font-semibold hover:bg-metalic-blue/90'
                        disabled={isSubmitting}
                    >
                        Save
                    </Button>
                </div>
            </form>
        </Form>
    );
}

type ValidationMessageProps = {
    children: ReactNode;
    status: 'success' | 'error';
};

function ValidationMessage({ children, status }: ValidationMessageProps) {
    return (
        <div className='flex items-center gap-2'>
            <img src={status === 'success' ? SuccessIcon : ErrorIcon} />
            <p
                className={cn(
                    status === 'success' ? 'text-green-500' : 'text-red-500',
                )}
            >
                {children}
            </p>
        </div>
    );
}

export default ChangePasswordForm;
