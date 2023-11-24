import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';

import { Form } from '@/components/ui/form';
import { MailIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FormInput } from '@/components/auth';

const forgotPasswordSchema = z.object({ email: z.string().email() });

type ForgotPasswordFields = z.infer<typeof forgotPasswordSchema>;
const defaultValues: ForgotPasswordFields = {
    email: '',
};

function ForgotPasswordForm() {
    const form = useForm({
        defaultValues,
        resolver: zodResolver(forgotPasswordSchema),
    });

    const onSubmit = form.handleSubmit(() => {});

    return (
        <div className='mx-auto grid w-full max-w-[530px] place-items-center p-4'>
            <Form {...form}>
                <form onSubmit={onSubmit} className='space-y-8'>
                    <div className='space-y-8 text-center'>
                        <h1 className='text-[52px] font-bold text-metalic-blue'>
                            Forgot Password?
                        </h1>
                        <p className='text-xl text-[#aaaaaa]'>
                            Don’t worry! Just enter your email address below and
                            we’ll send an instruction to reset your password.
                        </p>
                    </div>
                    <div>
                        <FormInput
                            name='email'
                            control={form.control}
                            icon={MailIcon}
                            placeholder='Email'
                        />
                    </div>
                    <div>
                        <p className='text-center text-lg'>
                            Didn’t receive a code?{' '}
                            <button
                                type='button'
                                className='text-jasper-orange hover:underline'
                            >
                                Resend
                            </button>{' '}
                            or{' '}
                            <button
                                type='button'
                                className='text-jasper-orange hover:underline'
                            >
                                Send to my mobile
                            </button>
                            .
                        </p>
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            className='h-[73px] w-[283px] bg-metalic-blue text-lg font-semibold uppercase hover:bg-metalic-blue/90'
                        >
                            Submit
                        </Button>
                    </div>
                </form>
            </Form>
        </div>
    );
}

export default ForgotPasswordForm;
