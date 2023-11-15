import { z } from 'zod';
import { useState } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { EyeIcon, MailIcon } from 'lucide-react';

import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import { FormInput } from '@/components/auth';

const loginSchema = z.object({
    email: z.string().email(),
    password: z.string().min(8).max(16),
});

type LoginFormFields = z.infer<typeof loginSchema>;

const defaultValues: LoginFormFields = {
    email: '',
    password: '',
};

function LoginForm() {
    const [errorMessages, setErrorMessages] = useState<Record<
        string,
        string
    > | null>(null);

    const form = useForm<LoginFormFields>({
        defaultValues,
        resolver: zodResolver(loginSchema),
    });

    const onSubmit = form.handleSubmit((values) => {
        router.post('/login', values, {
            onError: (error) => setErrorMessages(error),
        });
    });

    return (
        <Form {...form}>
            <div className='mx-auto grid w-full max-w-[640px] place-items-center'>
                <form onSubmit={onSubmit} className='w-full space-y-8 p-10'>
                    <h1 className='text-center text-[52px] font-bold text-yinmn-blue'>
                        Log in
                    </h1>
                    <div className='space-y-4'>
                        <FormInput
                            name='email'
                            placeholder='Email'
                            control={form.control}
                            icon={MailIcon}
                        />
                        <FormInput
                            name='password'
                            placeholder='Password'
                            control={form.control}
                            icon={EyeIcon}
                            type='password'
                        />
                    </div>
                    <div className='flex items-center justify-between text-[18px]'>
                        {errorMessages && Object.keys(errorMessages).length && (
                            <div className='text-red-500'>
                                {Object.entries(errorMessages).map(
                                    ([key, value]) => (
                                        <p key={key}>{value}</p>
                                    ),
                                )}
                            </div>
                        )}
                        <Link
                            href='/forgot-password'
                            className='ml-auto inline-block font-medium text-picton-blue'
                        >
                            Forgot Password?
                        </Link>
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            className='h-[51px] w-[283px] bg-yinmn-blue text-lg font-semibold uppercase hover:bg-yinmn-blue/90'
                        >
                            log in
                        </Button>
                    </div>
                </form>
            </div>
        </Form>
    );
}

export default LoginForm;
