import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { EyeIcon } from 'lucide-react';
import { Form } from '@/components/ui/form';
import { FormInput } from '@/components/auth';
import { Button } from '@/components/ui/button';

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
        <div className='mx-auto grid w-full max-w-[520px] place-items-center p-4'>
            <Form {...form}>
                <form onSubmit={onSubmit} className='space-y-10'>
                    <div className='space-y-4 text-center'>
                        <h1 className='text-[52px] font-bold tracking-tighter text-yinmn-blue'>
                            Create new password
                        </h1>
                        <p className='text-xl text-[#aaaaaa]'>
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
                            className='h-[73px] w-[283px] bg-yinmn-blue uppercase hover:bg-yinmn-blue/90'
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
