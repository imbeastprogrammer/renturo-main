import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { EyeIcon, MailIcon } from 'lucide-react';

import { Form } from '@/components/ui/form';
import FormInput from '@/components/forms/FormInput';
import { Button } from '@/components/ui/button';

const loginSchema = z.object({
    email: z.string().nonempty(),
    password: z.string().nonempty(),
});

type LoginFormFields = z.infer<typeof loginSchema>;

const defaultValues: LoginFormFields = {
    email: '',
    password: '',
};

function LoginForm() {
    const form = useForm<LoginFormFields>({
        defaultValues,
        resolver: zodResolver(loginSchema),
    });

    const onSubmit = form.handleSubmit(() => {});

    return (
        <Form {...form}>
            <div className='grid place-items-center'>
                <form
                    onSubmit={onSubmit}
                    className='w-full space-y-8 p-10 px-20'
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
                            placeholder='Password'
                            control={form.control}
                            icon={EyeIcon}
                        />
                    </div>
                    <Link
                        href='/forgot-password'
                        className='block text-right text-[18px] font-medium text-jasper-orange'
                    >
                        Forgot Password?
                    </Link>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            className='bg-metalic-blue px-24 py-7 uppercase hover:bg-metalic-blue/90'
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
