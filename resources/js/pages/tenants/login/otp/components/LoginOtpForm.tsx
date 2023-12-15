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

const DEFAULT_COUNTDOWN_TIMER = 300;

function LoginOtpForm() {
    const [isDisabled, setDisabled] = useState(true);
    const { timeRemaining, reset } = useCountdown(DEFAULT_COUNTDOWN_TIMER);

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
                        <h1 className='text-[52px] font-bold  text-metalic-blue'>
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
                        {timeRemaining > 0 ? (
                            <p className='text-[16px] text-black/50'>
                                Resend in {timeRemaining}s
                            </p>
                        ) : (
                            <Button
                                variant='link'
                                onClick={() => reset(5)}
                                className='h-auto py-0 text-base text-jasper-orange hover:underline'
                            >
                                Resend
                            </Button>
                        )}
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            disabled={isDisabled}
                            className='h-[73px] w-[283px] bg-metalic-blue uppercase hover:bg-metalic-blue/90'
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
