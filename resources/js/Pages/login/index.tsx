import * as z from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
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
        console.log(values);
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
                    <FormField
                        control={form.control}
                        name="email"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>Email</FormLabel>
                                <FormControl>
                                    <Input {...field} />
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
                                <FormLabel>Password</FormLabel>
                                <FormControl>
                                    <Input {...field} />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                    <div className="grid gap-4 place-items-center">
                        <Button
                            type="submit"
                            className="bg-metalic-blue uppercase px-20 py-6"
                        >
                            Submit
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
