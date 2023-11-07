import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { EyeIcon } from 'lucide-react';
import { Form } from '@/components/ui/form';
import FormInput from '@/components/forms/FormInput';
import { Button } from '@/components/ui/button';
import KabootekTextLogoBlue from '@/assets/central/auth/kabootek-text-logo-blue.png';

const createNewPasswordSchema = z
    .object({
        password: z.string().min(8).max(8),
        confirm_password: z.string().min(8).max(8),
    })
    .superRefine(({ password, confirm_password }, ctx) => {
        if (password !== confirm_password)
            ctx.addIssue({
                message: 'Password does not match',
                code: 'custom',
                path: ['confirm_password'],
            });
    });

type CreateNewPasswordFormFields = z.infer<typeof createNewPasswordSchema>;
const defaultValues: CreateNewPasswordFormFields = {
    password: '',
    confirm_password: '',
};

function CreateNewPasswordForm() {
    const form = useForm<CreateNewPasswordFormFields>({
        defaultValues,
        resolver: zodResolver(createNewPasswordSchema),
    });

    const onSubmit = form.handleSubmit(() => {});

    return (
        <div className='relative grid place-items-center p-4 px-8'>
            <img
                src={KabootekTextLogoBlue}
                alt='logo'
                className='absolute right-0 top-0 h-[36px]'
            />
            <Form {...form}>
                <form onSubmit={onSubmit} className='space-y-8'>
                    <div className='text-center'>
                        <h1 className='text-yinmn-blue text-[52px] font-bold'>
                            Create new password
                        </h1>
                        <p className='text-[20px] text-[#aaaaaa]'>
                            Your password must be at least 8 characters.
                        </p>
                    </div>
                    <div className='space-y-6'>
                        <FormInput
                            control={form.control}
                            name='password'
                            placeholder='Password'
                            label='Password'
                            icon={EyeIcon}
                            type='password'
                        />
                        <FormInput
                            label='Confirm Password'
                            control={form.control}
                            name='confirm_password'
                            placeholder='Confirm Password'
                            icon={EyeIcon}
                            type='password'
                        />
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            className='bg-yinmn-blue hover:bg-yinmn-blue/90 px-24 py-7 uppercase'
                        >
                            Submit
                        </Button>
                    </div>
                </form>
            </Form>
        </div>
    );
}

export default CreateNewPasswordForm;
