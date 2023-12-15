import _ from 'lodash';
import { z } from 'zod';
import { router } from '@inertiajs/react';
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
    const [isResending, setIsResending] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const { timeRemaining, reset } = useCountdown(DEFAULT_COUNTDOWN_TIMER);
    const [errorMessage, setErrorMessage] = useState('');
    const form = useForm<LoginOtpFormFields>({ defaultValues });

    const disabled = form.watch('verification_code').length !== 4;

    const onSubmit = form.handleSubmit((values) =>
        router.put(
            '/verify/mobile',
            { code: values.verification_code },
            {
                onBefore: () => setIsSubmitting(true),
                onSuccess: () => reset(DEFAULT_COUNTDOWN_TIMER),
                onError: (err) => setErrorMessage(_.valuesIn(err)[0]),
                onFinish: () => setIsSubmitting(false),
            },
        ),
    );

    const onSubmitOnComplete = (value: string) => {
        form.setValue('verification_code', value);
        router.put(
            '/verify/mobile',
            { code: value },
            {
                onBefore: () => setIsSubmitting(true),
                onSuccess: () => reset(DEFAULT_COUNTDOWN_TIMER),
                onError: (err) => setErrorMessage(_.valuesIn(err)[0]),
                onFinish: () => setIsSubmitting(false),
            },
        );
    };

    const handleResend = () => {
        router.post(
            '/resend/mobile/verification',
            {},
            {
                onBefore: () => setIsResending(true),
                onSuccess: () => reset(DEFAULT_COUNTDOWN_TIMER),
                onError: (err) => setErrorMessage(_.valuesIn(err)[0]),
                onFinish: () => setIsResending(false),
            },
        );
    };

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
                                    onComplete={(values) =>
                                        onSubmitOnComplete(values)
                                    }
                                    value={field.value}
                                />
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    {!!errorMessage && (
                        <p className='text-red-500'>{errorMessage}</p>
                    )}
                    <div className='space-y-2'>
                        <p className='text-[18px]'>Didn’t receive any OTP?</p>
                        {timeRemaining > 0 ? (
                            <p className='text-[16px] text-black/50'>
                                Resend in {timeRemaining}s
                            </p>
                        ) : (
                            <Button
                                variant='link'
                                onClick={handleResend}
                                disabled={isResending}
                                className='h-auto py-0 text-base text-jasper-orange hover:underline'
                            >
                                Resend
                            </Button>
                        )}
                    </div>
                    <div className='grid place-items-center'>
                        <Button
                            type='submit'
                            disabled={disabled || isSubmitting}
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
