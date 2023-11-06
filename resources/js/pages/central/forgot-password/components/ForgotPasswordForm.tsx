import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';

import RenturoLogoBlue from '@/assets/logo/RenturoLogoBlue.png';
import { Form } from '@/components/ui/form';
import { MailIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import FormInput from '@/components/forms/FormInput';

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
        <div className='relative grid place-items-center p-4 px-20'>
            <img
                className='absolute right-4 top-4 h-[36px]'
                src={RenturoLogoBlue}
                alt='logo'
            />
            <Form {...form}>
                <form onSubmit={onSubmit} className='space-y-8'>
                    <div>
                        <h1 className='text-[52px] font-bold text-metalic-blue'>
                            Forgot Password?
                        </h1>
                        <p className='text-[20px] text-[#aaaaaa]'>
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
                        <p className='text-center text-[18px]'>
                            Didn’t receive a code?{' '}
                            <button
                                type='button'
                                className='text-metalic-blue hover:underline'
                            >
                                Resend
                            </button>{' '}
                            or{' '}
                            <button
                                type='button'
                                className='text-metalic-blue hover:underline'
                            >
                                Send to my mobile
                            </button>
                            .
                        </p>
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            className='bg-metalic-blue px-24 py-7 uppercase hover:bg-metalic-blue/90'
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
