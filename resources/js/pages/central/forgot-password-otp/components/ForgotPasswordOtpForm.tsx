import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
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

const forgotPasswordOtpSchema = z.object({
    verification_code: z.string().min(4).max(4),
});

type ForgotPasswordOtpFormFields = z.infer<typeof forgotPasswordOtpSchema>;

const defaultValues: ForgotPasswordOtpFormFields = {
    verification_code: '',
};

function ForgotPasswordOtpForm() {
    const { countdown, reset } = useCountdown(5);
    const form = useForm<ForgotPasswordOtpFormFields>({
        defaultValues,
        resolver: zodResolver(forgotPasswordOtpSchema),
    });

    const isVerifiyButtonDisabled = !forgotPasswordOtpSchema.safeParse(
        form.watch(),
    ).success;

    const onSubmit = form.handleSubmit(() => {});

    return (
        <div className='relative grid place-items-center p-4 px-10 text-center'>
            <img
                src={KabootekTextLogoBlue}
                alt='logo'
                className='absolute right-4 top-4 h-[36px]'
            />
            <Form {...form}>
                <form onSubmit={onSubmit} className='mt-8 space-y-8'>
                    <div>
                        <h1 className='text-yinmn-blue text-[52px] font-bold'>
                            Enter OTP
                        </h1>
                        <p className='text-[20px] text-[#aaaaaa]'>
                            Please enter the{' '}
                            <span className='font-medium text-black'>
                                4-digit
                            </span>{' '}
                            code sent to{' '}
                            <span className='font-medium text-black'>
                                email@email.com
                            </span>
                        </p>
                    </div>
                    <div>
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
                    </div>
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
                            disabled={isVerifiyButtonDisabled}
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

export default ForgotPasswordOtpForm;
