import * as z from "zod";
import { Link, router } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormMessage,
} from "@/components/ui/form";
import { EyeIcon, MailIcon } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

import loginLogo from "@/assets/login-logo.png";
import loginHero from "@/assets/login-hero.png";

const formSchema = z.object({
    email: z.string().email(),
    password: z.string().min(8).max(32),
});

function LoginPage() {
    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: "",
            password: "",
        },
    });

    const onSubmit = (values: z.infer<typeof formSchema>) => {
        router.visit("/admin?active=Dashboard", { replace: true });
    };

    return (
        <div className="p-4 bg-metalic-blue h-screen grid place-items-center">
            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className="space-y-8 relative bg-white w-full max-w-xl p-12 rounded-2xl shadow-sm"
                >
                    <img
                        src={loginLogo}
                        className="mx-auto h-[50px] object-contain mb-10"
                    />
                    <div className="flex items-end justify-between">
                        <div>
                            <h1 className="text-metalic-blue text-headline-2">
                                Login
                            </h1>
                            <h2 className="text-headline-3 font-normal">
                                Welcome Back!
                            </h2>
                        </div>
                        <img src={loginHero} className="w-[250px]" />
                    </div>
                    <div className="grid gap-4">
                        <FormField
                            control={form.control}
                            name="email"
                            render={({ field }) => (
                                <FormItem>
                                    <FormControl>
                                        <div className="flex gap-2 relative items-center">
                                            <Input
                                                placeholder="Email"
                                                className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                                {...field}
                                            />
                                            <MailIcon className="absolute right-6 text-gray-400" />
                                        </div>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <FormField
                            control={form.control}
                            name="password"
                            render={({ field }) => (
                                <FormItem>
                                    <FormControl>
                                        <div className="flex gap-2 relative items-center">
                                            <Input
                                                type="password"
                                                placeholder="Password"
                                                className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                                {...field}
                                            />
                                            <EyeIcon className="absolute right-6 text-gray-400" />
                                        </div>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />
                        <Link
                            href="/forgot-password"
                            className="text-orange-500 inline-block ml-auto"
                        >
                            Forgot Password?
                        </Link>
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

export default LoginPage;
