import _ from 'lodash';
import * as z from 'zod';
import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { EyeIcon, MailIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import { FormInput } from '@/components/auth';

const formSchema = z.object({
    email: z.string().email(),
    password: z.string().min(8).max(32),
});

function LoginPage() {
    const [errors, setErrors] = useState<Record<string, string> | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: '',
            password: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.post('/login', values, {
            onBefore: () => setIsSubmitting(true),
            onFinish: () => setIsSubmitting(false),
            onError: (errors) => setErrors(errors),
        });
    };

    return (
        <Form {...form}>
            <div className='mx-auto grid w-full max-w-[640px] place-items-center'>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className='relative w-full space-y-8 p-10'
                >
                    <h1 className='text-center text-[52px] font-bold text-metalic-blue'>
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
                            type='password'
                            placeholder='Password'
                            control={form.control}
                            icon={EyeIcon}
                        />
                    </div>
                    <div className='flex items-center justify-between gap-4'>
                        {errors &&
                            _.valuesIn(errors).map((err) => (
                                <p className='text-center text-lg text-red-500'>
                                    {err}
                                </p>
                            ))}
                        <Link
                            href='/forgot-password'
                            className='ml-auto block text-lg font-medium text-jasper-orange'
                        >
                            Forgot Password?
                        </Link>
                    </div>
                    <div className='grid place-items-center gap-4'>
                        <Button
                            type='submit'
                            disabled={isSubmitting}
                            className='h-[51px] w-[283px] bg-metalic-blue text-lg font-semibold uppercase hover:bg-metalic-blue/90'
                        >
                            log in
                        </Button>
                    </div>
                </form>
            </div>
        </Form>
    );
}

export default LoginPage;
