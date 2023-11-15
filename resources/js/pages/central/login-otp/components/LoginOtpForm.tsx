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
import { FormPinInput } from '@/components/auth';
import useCountdown from '@/hooks/useCountdown';

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
        <div className='mx-auto grid max-w-[610px] place-items-center p-4'>
            <Form {...form}>
                <form
                    onSubmit={onSubmit}
                    className='mt-10 space-y-6 text-center'
                >
                    <div className='space-y-4'>
                        <h1 className='text-[52px] font-bold  text-yinmn-blue'>
                            Enter OTP
                        </h1>
                        <p className='text-xl text-[#aaaaaa]'>
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
                            <FormItem className='mx-auto max-w-[300px]'>
                                <FormLabel className='text-xl text-[#aaaaaa]'>
                                    Verification Code
                                </FormLabel>
                                <FormPinInput
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
                                className='text-picton-blue hover:underline'
                            >
                                Resend
                            </button>
                        )}
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            disabled={isDisabled}
                            className='h-[73px] w-[283px] bg-yinmn-blue uppercase hover:bg-yinmn-blue/90'
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
