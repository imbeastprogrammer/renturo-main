import { z } from 'zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';

import {
    Form,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import PinInput from '@/components/PinInput';
import useCountdown from '@/hooks/useCountdown';
import KabootekTextLogoBlue from '@/assets/central/auth/kabootek-text-logo-blue.png';

const loginOtpSchema = z.object({
    verification_code: z.string().min(4).max(4),
});

type LoginOtpFormFields = z.infer<typeof loginOtpSchema>;
const defaultValues: LoginOtpFormFields = { verification_code: '' };

function LoginOtpForm() {
    const [isDisabled, setDisabled] = useState(true);
    const { countdown, reset } = useCountdown(5);
    const form = useForm<LoginOtpFormFields>({ defaultValues });

    const onSubmit = form.handleSubmit((values) => {
        setDisabled(false);
    });

    return (
        <div className='relative grid place-items-center p-4 px-20'>
            <Form {...form}>
                <img
                    className='absolute right-0 top-0 h-[36px]'
                    src={KabootekTextLogoBlue}
                    alt='logo'
                />
                <form
                    onSubmit={onSubmit}
                    className='mt-10 space-y-6 text-center'
                >
                    <div className='space-y-4'>
                        <h1 className='text-yinmn-blue text-[52px]  font-bold'>
                            Enter OTP
                        </h1>
                        <p className='text-[20px] text-[#aaaaaa]'>
                            We’ve sent a{' '}
                            <span className='font-medium text-black'>
                                4-digit
                            </span>{' '}
                            code to you mobile number to verify its you.
                        </p>
                    </div>
                    <FormField
                        name='verification_code'
                        control={form.control}
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel className='text-[20px] text-[#aaaaaa]'>
                                    Verification Code
                                </FormLabel>
                                <PinInput
                                    length={4}
                                    secret={false}
                                    onChange={field.onChange}
                                    onComplete={() => onSubmit()}
                                    value={field.value}
                                />
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <div className='space-y-2'>
                        <p className='text-[18px]'>Didn’t receive any OTP?</p>
                        {countdown > 0 ? (
                            <p className='text-[16px] text-black/50'>
                                Resend in {countdown}s
                            </p>
                        ) : (
                            <button
                                onClick={() => reset(5)}
                                className='text-metalic-blue hover:underline'
                            >
                                Resend
                            </button>
                        )}
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            disabled={isDisabled}
                            className='bg-yinmn-blue hover:bg-yinmn-blue/90 px-24 py-7 uppercase'
                        >
                            verify
                        </Button>
                    </div>
                </form>
            </Form>
        </div>
    );
}

export default LoginOtpForm;
