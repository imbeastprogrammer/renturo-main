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
    pin: z.string().min(4).max(4),
});

function EnterPinPage() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {},
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
                    <h1 className="text-headline-3 font-normal">
                        Enter you PIN
                    </h1>
                    <FormField
                        control={form.control}
                        name="pin"
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

export default EnterPinPage;
