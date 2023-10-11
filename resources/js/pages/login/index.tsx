import * as z from 'zod';
import { Link, router } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form } from '@/components/ui/form';
import { EyeIcon, MailIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';

import loginLogo from '@/assets/login-logo.png';
import loginHero from '@/assets/login-hero.png';
import FormInput from '@/components/forms/FormInput';

const formSchema = z.object({
    email: z.string().email(),
    password: z.string().min(8).max(32),
});

type LoginPageProps = {
    errors: { email: string };
};
function LoginPage({ errors }: LoginPageProps) {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: '',
            password: '',
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.post('/login', values);
    };

    return (
        <div className='grid h-screen place-items-center bg-metalic-blue p-4'>
            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className='relative w-full max-w-xl space-y-8 rounded-2xl bg-white p-12 shadow-sm'
                >
                    <img
                        src={loginLogo}
                        className='mx-auto mb-10 h-[50px] object-contain'
                    />
                    <div className='flex items-end justify-between'>
                        <div>
                            <h1 className='text-headline-2 text-metalic-blue'>
                                Login
                            </h1>
                            <h2 className='text-headline-3 font-normal'>
                                Welcome Back!
                            </h2>
                        </div>
                        <img src={loginHero} className='w-[250px]' />
                    </div>
                    <div className='grid gap-4'>
                        {errors.email && (
                            <p className='text-center text-red-500'>
                                {errors.email}
                            </p>
                        )}
                        <FormInput
                            name='email'
                            label='Email'
                            control={form.control}
                            icon={MailIcon}
                        />
                        <FormInput
                            name='password'
                            label='Password'
                            type='password'
                            control={form.control}
                            icon={EyeIcon}
                        />
                        <Link
                            href='/forgot-password'
                            className='ml-auto inline-block text-orange-500'
                        >
                            Forgot Password?
                        </Link>
                    </div>
                    <div className='grid place-items-center gap-4'>
                        <Button
                            type='submit'
                            className='bg-metalic-blue px-20 py-6 uppercase hover:bg-metalic-blue/90'
                        >
                            log in
                        </Button>
                        <span className='absolute bottom-4 mx-auto inline-block text-xs'>
                            version 1.0
                        </span>
                    </div>
                </form>
            </Form>
        </div>
    );
}

export default LoginPage;
