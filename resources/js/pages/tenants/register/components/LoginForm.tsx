import _ from 'lodash';
import * as z from 'zod';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { TbPassword } from 'react-icons/tb';
import { Button } from '@/components/ui/button';

import { FormInput } from '@/components/auth';
import { FaEnvelope, FaPhone, FaUser } from 'react-icons/fa6';

const formSchema = z
    .object({
        first_name: z.string().nonempty(),
        last_name: z.string().nonempty(),
        email: z.string().email(),
        mobile_number: z.string().min(11).max(11),
        password: z.string().min(8).max(32),
        confirm_password: z.string().min(8).max(32),
    })
    .superRefine((fields, ctx) => {
        if (fields.password !== fields.confirm_password)
            ctx.addIssue({
                path: ['confirm_password'],
                message: 'Password does not match',
                code: 'custom',
            });
    });

function LoginPage() {
    const [errors, setErrors] = useState<Record<string, string> | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            first_name: '',
            last_name: '',
            mobile_number: '',
            email: '',
            password: '',
            confirm_password: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        return;
        router.post('/register', values, {
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
                        Register
                    </h1>
                    <div className='space-y-4'>
                        <FormInput
                            name='first_name'
                            placeholder='First Name'
                            control={form.control}
                            icon={FaUser}
                        />
                        <FormInput
                            name='last_name'
                            placeholder='Last Name'
                            control={form.control}
                            icon={FaUser}
                        />
                        <FormInput
                            name='email'
                            placeholder='Email'
                            control={form.control}
                            icon={FaEnvelope}
                        />
                        <FormInput
                            name='mobile_number'
                            placeholder='Mobile Number'
                            control={form.control}
                            icon={FaPhone}
                        />
                        <FormInput
                            name='password'
                            type='password'
                            placeholder='Password'
                            control={form.control}
                            icon={TbPassword}
                        />
                        <FormInput
                            name='confirm_password'
                            type='password'
                            placeholder='Confirm Password'
                            control={form.control}
                            icon={TbPassword}
                        />
                    </div>
                    <div className='flex items-center'>
                        {errors &&
                            _.valuesIn(errors).map((err) => (
                                <p className='text-center text-lg text-red-500'>
                                    {err}
                                </p>
                            ))}
                    </div>
                    <div className='grid place-items-center gap-4'>
                        <Button
                            type='submit'
                            disabled={isSubmitting}
                            className='h-[51px] w-[283px] bg-metalic-blue text-lg font-semibold uppercase hover:bg-metalic-blue/90'
                        >
                            Register
                        </Button>
                    </div>
                </form>
            </div>
        </Form>
    );
}

export default LoginPage;
