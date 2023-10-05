import * as z from "zod";
import { router } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormMessage,
} from "@/components/ui/form";
import { Button } from "@/components/ui/button";

import loginLogo from "@/assets/login-logo.png";
import PinInput from "@/components/PinInput";

const formSchema = z.object({
    otp: z.string().min(4).max(4),
});

function EnterOtpPage() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: { otp: "" },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.visit("/admin?active=Dashboard", { replace: true });
    };

    return (
        <div className="p-4 bg-metalic-blue h-screen grid place-items-center">
            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className="space-y-8 min-h-[500px] relative bg-white w-full max-w-lg p-12 rounded-2xl shadow-sm"
                >
                    <img
                        src={loginLogo}
                        className="mx-auto h-[50px] object-contain mb-10"
                    />
                    <div className="space-y-2">
                        <h1 className="text-headline-3 font-normal">
                            Forgot Password
                        </h1>
                        <p>
                            We’ve sent a 4-digit code to your mobile number to
                            verify its you.
                        </p>
                    </div>
                    <FormField
                        control={form.control}
                        name="otp"
                        render={({ field }) => (
                            <FormItem>
                                <FormControl>
                                    <PinInput
                                        length={4}
                                        value={field.value}
                                        onChange={field.onChange}
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <div className="space-y-1 text-center text-xs text-heavy-carbon">
                        <p>Didn’t receive any OTP?</p>
                        <p>Resend in 300s</p>
                    </div>
                    <div className="grid gap-4 place-items-center">
                        <Button
                            type="submit"
                            className="bg-metalic-blue uppercase hover:bg-metalic-blue/90 px-20 py-6"
                        >
                            log in
                        </Button>
                        <span className="inline-block text-xs mx-auto absolute bottom-4">
                            version 1.0
                        </span>
                    </div>
                </form>
            </Form>
        </div>
    );
}

export default EnterOtpPage;
